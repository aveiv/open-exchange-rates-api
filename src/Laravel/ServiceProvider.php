<?php

namespace Aveiv\OpenExchangeRatesApi\Laravel;

use Aveiv\OpenExchangeRatesApi\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {
    public function boot() {
        $this->package('aveiv/open-exchange-rates-api', 'open-exchange-rates-api', __DIR__ . '/../../laravel');
    }

    public function register() {
        $this->app->bindShared('aveiv.open-exchange-rates-api.client', function ($app) {
            $appId = $app['config']->get('open-exchange-rates-api::app_id');
            return new Client($appId, new GuzzleClient());
        });
    }
}
