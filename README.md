## PHP wrapper for Open Exchange Rates API

[![Latest Stable Version](https://poser.pugx.org/aveiv/open-exchange-rates-api/v)](//packagist.org/packages/aveiv/open-exchange-rates-api) [![Total Downloads](https://poser.pugx.org/aveiv/open-exchange-rates-api/downloads)](//packagist.org/packages/aveiv/open-exchange-rates-api) [![License](https://poser.pugx.org/aveiv/open-exchange-rates-api/license)](//packagist.org/packages/aveiv/open-exchange-rates-api)

## Usage example

```php
$api = new OpenExchangeRates('YOUR_APP_ID');
// or $client = new OpenExchangeRates('YOUR_APP_ID', new YourHttpClient());


// Getting currencies

$api->currencies(); // returns ["USD" => "United States Dollar", ...]

$api->currencies([
    'show_alternative' => true, // include alternative currencies
    'show_inactive' => true,    // include historical/inactive currencies
]);


// Getting latest rates

$api->latest(); // returns ["USD" => 1.0, ...]

$api->latest([
    'base' => 'EUR',             // base currency
    'symbols' => ['CNY', 'USD'], // limit results to specific currencies
    'show_alternative' => true,  // include alternative currencies
]);


// Getting historical rates

$api->historical(new \DateTime('2020-01-01')); // ["USD" => 1.0, ...]

$api->historical(new \DateTime('2020-01-01'), [
    'base' => 'EUR',             // base currency
    'symbols' => ['CNY', 'USD'], // limit results to specific currencies
    'show_alternative' => true,  // include alternative currencies
]);
```
