<?php

declare(strict_types=1);

namespace Aveiv\OpenExchangeRatesApi;

use Aveiv\MixedValue\MixedValue;
use Aveiv\OpenExchangeRatesApi\Exception\ApiException;
use Aveiv\OpenExchangeRatesApi\Exception\BadResponseException;

final class OpenExchangeRates
{
    private const API_BASE_URL = 'https://openexchangerates.org/api';

    private string $appId;

    private HttpClientInterface $httpClient;

    /**
     * @param string $appId
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct(string $appId, ?HttpClientInterface $httpClient = null)
    {
        $this->appId = $appId;
        $this->httpClient = $httpClient ?? new CurlHttpClient();
    }

    /**
     * Available options:
     * - `show_alternative` - include alternative currencies;
     * - `show_inactive` - include historical/inactive currencies.
     *
     * @param array $options
     * @return array
     *
     * @psalm-param array{show_alternative?: bool, show_inactive?: bool} $options
     * @psalm-return array<string, string>
     */
    public function currencies(array $options = []): array
    {
        $optsMixed = new MixedValue($options);
        $res = $this->sendRequest('/currencies.json', [
            'show_alternative' => $optsMixed['show_alternative']->isBool()->findValue(),
            'show_inactive' => $optsMixed['show_inactive']->isBool()->findValue(),
        ]);
        $currencies = [];
        assert(is_array($res));
        foreach ($res as $code => $name) {
            assert(is_string($code));
            assert(is_string($name));
            $currencies[$code] = $name;
        }
        return $currencies;
    }

    /**
     * Available options:
     * - `base` - base currency (default: USD);
     * - `symbols` - limit results to specific currencies;
     * - `show_alternative` - extend returned values with alternative, black market and digital currency rates.
     *
     * @param array $options
     * @return array
     *
     * @psalm-param array{base?: string, symbols?: array<string>, show_alternative?: bool} $options
     * @psalm-return array<string, float>
     */
    public function latest(array $options = []): array
    {
        $optsMixed = new MixedValue($options);
        $res = $this->sendRequest('/latest.json', [
            'base' => $optsMixed['base']->isString()->findValue(),
            'symbols' => $optsMixed['symbols']->map(fn(MixedValue $el) => $el->isString()->getValue())->findValue(),
            'show_alternative' => $optsMixed['show_alternative']->isBool()->findValue(),
        ]);
        return $this->fetchRates($res);
    }

    /**
     * Available options:
     * - `base` - base currency (default: USD);
     * - `symbols` - limit results to specific currencies;
     * - `show_alternative` - extend returned values with alternative, black market and digital currency rates.
     *
     * @param \DateTimeInterface $dateTime
     * @param array $options
     * @return array
     *
     * @psalm-param array{base?: string, symbols?: array<string>, show_alternative?: bool} $options
     * @psalm-return array<string, float>
     */
    public function historical(\DateTimeInterface $dateTime, array $options = []): array
    {
        $optsMixed = new MixedValue($options);
        $path = sprintf('/historical/%s.json', $dateTime->format('Y-m-d'));
        $res = $this->sendRequest($path, [
            'base' => $optsMixed['base']->isString()->findValue(),
            'symbols' => $optsMixed['symbols']->map(fn(MixedValue $el) => $el->isString()->getValue())->findValue(),
            'show_alternative' => $optsMixed['show_alternative']->isBool()->findValue(),
        ]);
        return $this->fetchRates($res);
    }

    /**
     * @param mixed $res
     * @return array
     *
     * @psalm-return array<string, float>
     */
    private function fetchRates($res): array
    {
        $rates = [];
        assert(is_array($res));
        assert(isset($res['rates']));
        assert(is_array($res['rates']));
        foreach ($res['rates'] as $code => $rate) {
            assert(is_string($code));
            assert(is_int($rate) || is_float($rate));
            $rates[$code] = floatval($rate);
        }
        return $rates;
    }

    /**
     * @param string $path
     * @param array $query
     * @return mixed
     *
     * @psalm-param array<null>|array<scalar>|array<array<string>> $query
     */
    private function sendRequest(string $path, array $query = [])
    {
        $query['app_id'] = $this->appId;
        $query['prettyprint'] = false;
        $url = $this->buildUrl($path, $query);
        $res = $this
            ->httpClient
            ->get($url);

        $res = json_decode($res, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadResponseException(sprintf('Failed to parse json: "%s"', json_last_error_msg()));
        }

        $resMixed = new MixedValue($res);

        if ($resMixed['error']->toBool()->findValue()) {
            throw new ApiException(
                $resMixed['description']->toString()->getValue(),
                $resMixed['status']->toInt()->getValue(),
            );
        }

        return $res;
    }

    /**
     * @param string $path
     * @param array|null $query
     * @return string
     *
     * @psalm-param array<null>|array<scalar>|array<array<string>> $query
     */
    private function buildUrl(string $path, array $query = null): string
    {
        $url = static::API_BASE_URL . $path;
        if ($query) {
            $query = array_map(function ($q) {
                if (is_array($q)) {
                    return implode(',', $q);
                }
                return $q;
            }, $query);
            $url .= '?' . http_build_query($query);
        }
        return $url;
    }
}
