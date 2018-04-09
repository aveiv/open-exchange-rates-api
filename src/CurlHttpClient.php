<?php

namespace Aveiv\OpenExchangeRatesApi;

class CurlHttpClient implements HttpClientInterface
{
    public function get($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $resp = curl_exec($ch);
        return $resp;
    }
}
