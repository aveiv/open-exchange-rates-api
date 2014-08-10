<?php

namespace Aveiv\OpenExchangeRatesApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Query;
use GuzzleHttp\Url;

class Client {
    const API_SCHEME = 'https';
    const API_HOST = 'openexchangerates.org';
    const API_PATH = 'api';

    /**
     * @var string
     */
    private $appId;
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @param string $appId
     * @param ClientInterface $guzzleClient
     */
    public function __construct($appId, ClientInterface $guzzleClient) {
        $this->appId = $appId;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return $this->sendRequest('currencies.json');
    }

    /**
     * @param string|null $base
     * @return array
     */
    public function getLatest($base = null) {
        if ($base) {
            $body = $this->sendRequest('latest.json', ['base' => $base]);
        } else {
            $body = $this->sendRequest('latest.json');
        }
        return $body;
    }

    /**
     * @param \DateTimeInterface|string $date
     * @param string|null $base
     * @return array
     */
    public function getHistorical($date, $base = null) {
        $path = ['historical'];
        if ($date instanceof \DateTimeInterface) {
            $path[] = $date->format('Y-m-d') . '.json';
        } else {
            $path[] = $date . '.json';
        }
        $path = implode('/', $path);
        if ($base) {
            $body = $this->sendRequest($path, ['base' => $base]);
        } else {
            $body = $this->sendRequest($path);
        }
        return $body;
    }

    /**
     * @param string $path
     * @param Query|array|string|null $query
     * @return array
     * @throws RequestException
     * @throws ApiException
     */
    protected function sendRequest($path, $query = null) {
        $url = $this->makeUrl($path, $query);
        $badRequestMsg = 'Bad request: ' . $url;
        try {
            $response = $this->guzzleClient->get($url, ['allow_redirects' => false]);
        } catch (ClientException $e) {
            if ($e->hasResponse() && ($body = json_decode($e->getResponse()->getBody(), true))) {
                if (isset($body['error']) && $body['error']) {
                    throw new ApiException($body['description'], $body['message'], $body['status']);
                }
            }
            throw new RequestException($badRequestMsg, $e->getCode());
        }
        if (200 != $response->getStatusCode()) {
            throw new RequestException($badRequestMsg, $response->getStatusCode());
        }
        $body = json_decode($response->getBody(), true);
        return $body;
    }

    /**
     * @param string $path
     * @param Query|array|string|null $query
     * @return Url
     */
    protected function makeUrl($path, array $query = null) {
        $url = new Url(self::API_SCHEME, self::API_HOST);
        $url->addPath(self::API_PATH);
        $url->addPath($path);
        if ($query) {
            $url->setQuery($query);
        }
        $url->getQuery()->add('app_id', $this->appId);
        return $url;
    }
}
