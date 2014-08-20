# PHP wrapper for [Open Exchange Rates](https://openexchangerates.org) API
## Example
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Aveiv\OpenExchangeRatesApi\Client;
use GuzzleHttp\Client as GuzzleClient;


$client = new Client('YOUR_APP_ID', new GuzzleClient());

// Get currency list
print_r($client->getCurrencies());

// Get latest rates
print_r($client->getLatest());

// Get latest rates with custom base currency
print_r($client->getLatest('EUR'));

// Get rates by date
print_r($client->getHistorical(new DateTime()));

// Get rates by date with custom base currency
print_r($client->getHistorical(new DateTime(), 'EUR'));
```
## Laravel integration
### Configuration
Add the service provider to the providers array in app/config/app.php:
```
'Aveiv\OpenExchangeRatesApi\Laravel\ServiceProvider'
```
Add the facade to the facades array in app/config/app.php:
```
'Rates' => 'Aveiv\OpenExchangeRatesApi\Laravel\Facade',
```
Publish the configuration file:
```
./artisan config:publish --path="/path_to_vendor/aveiv/open-exchange-rates-api/laravel/config/" aveiv/open-exchange-rates-api
```
Change the "app_id" option in the configuration file.
### Example
```php
<?php

// Get currency list
print_r(Rates::getCurrencies());

// etc
```
