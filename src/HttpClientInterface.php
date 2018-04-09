<?php

namespace Aveiv\OpenExchangeRatesApi;

interface HttpClientInterface
{
    /**
     * @param string $url Request URL.
     * @return string Respnse body.
     */
    public function get($url);
}
