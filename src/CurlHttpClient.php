<?php

declare(strict_types=1);

namespace Aveiv\OpenExchangeRatesApi;

use Aveiv\OpenExchangeRatesApi\Exception\HttpException;

final class CurlHttpClient implements HttpClientInterface
{
    public function get(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new HttpException($err);
        }
        assert(is_string($res));
        return $res;
    }
}
