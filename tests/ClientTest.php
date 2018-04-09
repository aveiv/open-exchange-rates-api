<?php

namespace Aveiv\OpenExchangeRatesApi\Tests;

use Aveiv\OpenExchangeRatesApi\Client;
use Aveiv\OpenExchangeRatesApi\CurlHttpClient;
use Aveiv\OpenExchangeRatesApi\Exception\Exception;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = $this->buildClient();
    }

    protected function buildClient($apiKey = API_KEY)
    {
        return new Client($apiKey, new CurlHttpClient());
    }

    public function testCurrencies()
    {
        $currencies = $this->client->getCurrencies();
        $this->assertInternalType('array', $currencies);
        $this->assertArrayHasKey('USD', $currencies);
    }

    public function testLatest()
    {
        $latest = $this->client->getLatest();
        $this->assertInternalType('array', $latest);
        $this->assertArrayHasKey('base', $latest);
    }

    public function testLatestWithBase()
    {
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
        }
        $latest = $this->client->getLatest('EUR');
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $latest);
            $this->assertArrayHasKey('base', $latest);
            $this->assertTrue($latest['base'] == 'EUR');
        }
    }

    public function testLatestWithInvalidBase()
    {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
            $this->client->getLatest('BAD_BASE');
        }
    }

    public function testLatestWithLimit()
    {
        $latest = $this->client->getLatest(null, ['EUR', 'RUB']);
        $this->assertInternalType('array', $latest);
        $this->assertArrayHasKey('base', $latest);
    }

    public function testLatestWithInvalidLimit()
    {
        $latest = $this->client->getLatest(null, ['BAD_CURRENCY']);
        $this->assertInternalType('array', $latest);
        $this->assertArrayHasKey('base', $latest);
        $this->assertArrayHasKey('rates', $latest);
        $this->assertInternalType('array', $latest['rates']);
        $this->assertEmpty($latest['rates']);
    }

    public function testHistorical()
    {
        $dt = new \DateTime();
        $historical = $this->client->getHistorical($dt);
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
        $historical = $this->client->getHistorical($dt->format('Y-m-d'));
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
    }

    public function testHistoricalWithInvalidDate()
    {
        $this->setExpectedException(Exception::class);
        $this->client->getHistorical('BAD_DATE');
    }

    public function testHistoricalWithBase()
    {
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
        }
        $historical = $this->client->getHistorical(new \DateTime(), 'EUR');
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $historical);
            $this->assertArrayHasKey('base', $historical);
            $this->assertTrue('EUR' == $historical['base']);
        }
    }

    public function testHistroicalWithInvalidBase()
    {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
            $this->client->getHistorical(new \DateTime(), 'BAD_BASE');
        }
    }

    public function testHistoricalWithLimit()
    {
        $historical = $this->client->getHistorical(new \DateTime(), null, ['EUR', 'RUB']);
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
    }

    public function testHistoricalWithInvalidLimit()
    {
        $historical = $this->client->getHistorical(new \DateTime(), null, ['BAD_CURRENCY']);
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
        $this->assertArrayHasKey('rates', $historical);
        $this->assertInternalType('array', $historical['rates']);
        $this->assertEmpty($historical['rates']);
    }

    public function testInvalidAppId()
    {
        $client = $this->buildClient('BAD_APP_ID');
        $this->setExpectedException(Exception::class);
        $client->getCurrencies();
        $client->getLatest();
        $client->getHistorical(new \DateTime());
    }
}
