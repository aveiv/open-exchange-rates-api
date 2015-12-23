<?php

namespace Aveiv\OpenExchangeRatesApi\Tests;

use Aveiv\OpenExchangeRatesApi\Exception\Exception;
use Aveiv\OpenExchangeRatesApi\Client;
use GuzzleHttp\Client as GuzzleClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = new Client(API_KEY, new GuzzleClient());
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
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
        }
        $latest = $this->client->getLatest(null, ['EUR', 'RUB']);
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $latest);
            $this->assertArrayHasKey('base', $latest);
        }
    }

    public function testLatestWithInvalidLimit()
    {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
            $this->client->getLatest(null, ['BAD_CURRENCY']);
        }
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
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
        }
        $historical = $this->client->getHistorical(new \DateTime(), null, ['EUR', 'RUB']);
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $historical);
            $this->assertArrayHasKey('base', $historical);
        }
    }

    public function testHistoricalWithInvalidLimit()
    {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(Exception::class);
            $this->client->getHistorical(new \DateTime(), null, ['BAD_CURRENCY']);
        }
    }

    public function testInvalidAppId()
    {
        $client = new Client('BAD_APP_ID', new GuzzleClient());
        $this->setExpectedException(Exception::class);
        $client->getCurrencies();
        $client->getLatest();
        $client->getHistorical(new \DateTime());
    }
}
