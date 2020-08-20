<?php

declare(strict_types=1);

namespace Aveiv\OpenExchangeRatesApi;

interface HttpClientInterface
{
    public function get(string $url): string;
}
