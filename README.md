# Zillow

A package for working w/ the Zillow API.

## Install

Normal install via Composer.

## Usage

Call the method you want, passing the arguments as a single array.

```php
use Travis\Zillow;

$result = Zillow::get_search_results(array(
	'api_key' => 'foobar',
    'address' => '1600 Pennsylvania Ave NW',
    'citystatezip' => '20500',
));
```

See the API documentation [here](http://www.zillow.com/howto/api/APIOverview.htm).
