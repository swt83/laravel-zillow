# Zillow for Laravel

A package for working w/ the Zillow API.

## Install

Normal bundle install.

### Dependencies

* [XML](https://github.com/swt83/laravel-xml) - A package for working w/ XML files.

## Usage

Call the method you want, passing the arguments as a single array.  Find documentation [here](http://www.zillow.com/howto/api/APIOverview.htm).

```php
$result = Zillow::get_search_results(array(
    'address' => '777 Pearly Gates',
    'citystatezip' => '77777'
));
```

Process the response in your own way.