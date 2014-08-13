<?php

namespace Aveiv\OpenExchangeRatesApi\Laravel;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade {
    protected static function getFacadeAccessor() {
        return static::$app['aveiv.open-exchange-rates-api.client'];
    }
}
