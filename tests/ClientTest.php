<?php

namespace Aveiv\OpenExchangeRatesApi\Tests;

use Aveiv\OpenExchangeRatesApi\ApiException;
use Aveiv\OpenExchangeRatesApi\Client;
use GuzzleHttp\Client as GuzzleClient;

class ClientTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Client
     */
    protected $client;

    protected function setUp() {
        parent::setUp();
        $this->client = new Client(API_KEY, new GuzzleClient());
    }

    public function testCurrencies() {
        $currencies = $this->client->getCurrencies();
        $this->assertInternalType('array', $currencies);
        $this->assertArrayHasKey('USD', $currencies);
    }

    public function testLatest() {
        $latest = $this->client->getLatest();
        $this->assertInternalType('array', $latest);
        $this->assertArrayHasKey('base', $latest);
    }

    public function testLatestWithBase() {
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(ApiException::class);
        }
        $latest = $this->client->getLatest('EUR');
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $latest);
            $this->assertArrayHasKey('base', $latest);
            $this->assertTrue($latest['base'] == 'EUR');
        }
    }

    public function testLatestWithInvalidBase() {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(ApiException::class);
            $this->client->getLatest('FAKE_BASE');
        }
    }

    public function testHistorical() {
        $dt = new \DateTime();
        $historical = $this->client->getHistorical($dt);
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
        $historical = $this->client->getHistorical($dt->format('Y-m-d'));
        $this->assertInternalType('array', $historical);
        $this->assertArrayHasKey('base', $historical);
    }

    public function testHistoricalWithInvalidDate() {
        $this->setExpectedException(ApiException::class);
        $this->client->getHistorical('FAKE_DATE');
    }

    public function testHistoricalWithBase() {
        if (!ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(ApiException::class);
        }
        $historical = $this->client->getHistorical(new \DateTime(), 'EUR');
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->assertInternalType('array', $historical);
            $this->assertArrayHasKey('base', $historical);
            $this->assertTrue('EUR' == $historical['base']);
        }
    }

    public function testHistroicalWithInvalidBase() {
        if (ENTERPRISE_OR_UNLIMITED_ACCOUNT) {
            $this->setExpectedException(ApiException::class);
            $this->client->getHistorical(new \DateTime(), 'FAKE_BASE');
        }
    }

    public function testInvalidAppId() {
        $client = new Client('FAKE_APP_ID', new GuzzleClient());
        $this->setExpectedException(ApiException::class);
        $client->getCurrencies();
        $client->getLatest();
        $client->getHistorical(new \DateTime());
    }
}
