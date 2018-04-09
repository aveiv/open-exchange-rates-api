<?php

namespace Aveiv\OpenExchangeRatesApi;

use Aveiv\OpenExchangeRatesApi\Exception\Exception;

class Client
{
    const API_BASE_URL = 'https://openexchangerates.org/api';

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param string $appId
     * @param HttpClientInterface|null $httpClient
     */
    public function __construct($appId, HttpClientInterface $httpClient = null)
    {
        $this->appId = $appId;
        $this->httpClient = $httpClient;
        if (!$this->httpClient) {
            $this->httpClient = new CurlHttpClient();
        }
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getCurrencies()
    {
        return $this->sendRequest('/currencies.json');
    }

    /**
     * @param string|null $base
     * @param array $symbols
     * @return array
     * @throws Exception
     */
    public function getLatest($base = null, $symbols = [])
    {
        $query = [
            'base' => $base,
        ];
        if ($symbols) {
            $query['symbols'] = implode(',', $symbols);
        }
        $body = $this->sendRequest('/latest.json', $query);
        return $body;
    }

    /**
     * @param \DateTimeInterface|string $date
     * @param string|null $base
     * @param array $symbols
     * @return array
     * @throws Exception
     */
    public function getHistorical($date, $base = null, array $symbols = [])
    {
        $path = ['historical'];
        if ($date instanceof \DateTimeInterface) {
            $path[] = $date->format('Y-m-d');
        } else {
            $path[] = $date;
        }
        $path = '/' . implode('/', $path) . '.json';
        $query = [
            'base' => $base,
        ];
        if ($symbols) {
            $query['symbols'] = implode(',', $symbols);
        }
        $body = $this->sendRequest($path, $query);
        return $body;
    }

    /**
     * @param $path
     * @param array $query
     * @return mixed
     * @throws Exception
     */
    protected function sendRequest($path, array $query = [])
    {
        $query['app_id'] = $this->appId;
        $url = $this->buildUrl($path, $query);
        $resp = $this
            ->httpClient
            ->get($url);
        $body = $resp ? json_decode($resp, true) : null;
        if (!$body || (isset($body['error']) && $body['error'])) {
            $message = 'Network error.';
            $code = 0;
            if (isset($body['error'])) {
                $message = $body['description'];
                $code = $body['status'];
            }
            throw new Exception($message, $code);
        }
        return $body;
    }

    /**
     * @param string $path
     * @param array|null $query
     * @return string
     */
    protected function buildUrl($path, array $query = null)
    {
        $url = static::API_BASE_URL . $path;
        if ($query) {
            $url .= '?' . http_build_query($query);
        }
        return $url;
    }
}
