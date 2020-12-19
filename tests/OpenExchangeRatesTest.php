<?php

namespace Aveiv\OpenExchangeRatesApi\Tests;

use Aveiv\OpenExchangeRatesApi\OpenExchangeRates;
use Aveiv\OpenExchangeRatesApi\Exception\ApiException;
use PHPUnit\Framework\TestCase;

class OpenExchangeRatesTest extends TestCase
{
    private function buildClient($apiKey = null): OpenExchangeRates
    {
        if (!$apiKey) {
            $apiKey = defined('API_KEY') ? API_KEY : 'INVALID_API_KEY';
        }
        return new OpenExchangeRates($apiKey);
    }

    public function testThrowsApiExceptionIfApiReturnsError(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid App ID provided. Please sign up at https://openexchangerates.org/signup, or contact support@openexchangerates.org.');
        $this->expectExceptionCode(401);

        $client = $this->buildClient('INVALID_API_KEY');
        $client->latest();
    }

    public function testCurrencies_NoOptions_ReturnsCurrencies(): void
    {
        $client = $this->buildClient();
        $currencies = $client->currencies();
        $this->assertArrayHasKey('USD', $currencies);
        $this->assertIsString($currencies['USD']);
    }

    public function testCurrencies_ShowAlternativeOption_ReturnsCurrenciesIncludingAlternative(): void
    {
        $client = $this->buildClient();
        $currencies = $client->currencies([
            'show_alternative' => true,
        ]);
        $this->assertArrayHasKey('BTS', $currencies);
        $this->assertIsString($currencies['BTS']);
    }

    public function testCurrencies_ShowInactiveOption_ReturnsCurrenciesIncludingInactive(): void
    {
        $client = $this->buildClient();
        $currencies = $client->currencies([
            'show_inactive' => true,
        ]);
        $this->assertArrayHasKey('BYR', $currencies);
        $this->assertIsString($currencies['BYR']);
    }

    public function testLatest_NoOptions_ReturnsRates(): void
    {
        $client = $this->buildClient();
        $latest = $client->latest();
        $this->assertArrayHasKey('USD', $latest);
        $this->assertSame(1.0, $latest['USD']);
    }

    public function testLatest_BaseOption_ReturnsRatesWithCustomBase(): void
    {
        $client = $this->buildClient();
        $latest = $client->latest(['base' => 'EUR']);
        $this->assertArrayHasKey('EUR', $latest);
        $this->assertSame(1.0, $latest['EUR']);
    }

    public function testLatest_SymbolsOption_ReturnsRatesForSymbols(): void
    {
        $client = $this->buildClient();
        $latest = $client->latest(['symbols' => ['EUR']]);
        $this->assertCount(1, $latest);
        $this->assertArrayHasKey('EUR', $latest);
        $this->assertIsFloat($latest['EUR']);
    }

    public function testLatest_ShowAlternativeOption_ReturnsRatesIncludingAlternative(): void
    {
        $client = $this->buildClient();
        $latest = $client->latest(['show_alternative' => true]);
        $this->assertArrayHasKey('BTS', $latest);
        $this->assertIsFloat($latest['BTS']);
    }

    public function testHistorical_NoOptions_ReturnsHistoricalRates(): void
    {
        $client = $this->buildClient();
        $historical = $client->historical(new \DateTime('2020-01-01'));
        $this->assertArrayHasKey('USD', $historical);
        $this->assertSame(1.0, $historical['USD']);
        $this->assertArrayHasKey('EUR', $historical);
        $this->assertSame(0.891348, $historical['EUR']);
    }

    public function testHistorical_BaseOptions_ReturnsHistoricalRatesWithCustomBase(): void
    {
        $client = $this->buildClient();
        $historical = $client->historical(new \DateTime('2020-01-01'), ['base' => 'EUR']);
        $this->assertArrayHasKey('EUR', $historical);
        $this->assertSame(1.0, $historical['EUR']);
    }

    public function testHistorical_SymbolsOption_ReturnsRatesForSymbols(): void
    {
        $client = $this->buildClient();
        $historical = $client->historical(new \DateTime('2020-01-01'), ['symbols' => ['EUR']]);
        $this->assertCount(1, $historical);
        $this->assertArrayHasKey('EUR', $historical);
        $this->assertSame(0.891348, $historical['EUR']);
    }

    public function testHistorical_ShowAlternativeOption_ReturnsRatesIncludingAlternative(): void
    {
        $client = $this->buildClient();
        $historical = $client->historical(new \DateTime('2020-01-01'), ['show_alternative' => true]);
        $this->assertArrayHasKey('BTS', $historical);
        $this->assertSame(67.2811091787, $historical['BTS']);
    }

    public function testConvert(): void
    {
        $client = $this->buildClient();
        $convertedValue = $client->convert(99.99, 'USD', 'EUR');
        $this->assertIsFloat($convertedValue);
    }
}
