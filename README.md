# middlewares/honeypot

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to implement a honeypot spam prevention. This technique is based on creating a input field that should be invisible and left empty by real users but filled by most spam bots. The middleware check in the incoming requests whether this value exists and is empty (is a real user) or doesn't exist or has a value (is a bot) returning a 403 response.

## Requirements

* PHP >= 5.6
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http mesage implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/honeypot](https://packagist.org/packages/middlewares/honeypot).

```sh
composer require middlewares/honeypot
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\Honeypot(),

    function ($request) {
        $response = new Response();
        //Use Honeypot::getField() to generate honeypot fields
        $response->getBody()->write('<form>'.Honeypot::getField().'</form>');
        return $response;
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(string $name = "hpt_name")`

The name of the input field (by default is "hpt_name"). You can use the name to hide the input using css:

```css
input[name="hpt_name"] {
    display: none;
}
```

## Helpers

#### `Honeypot::getField($name = null)`

This static method is provided to ease the creation of the input field, accepting an optional `$name` argument. If it's not provided, use the same name passed previously to the middleware.

Example:

```html
<html>
    <head>
        <style type="text/css">
            input[name="hpt_name"] { display: none; }
        </style>
    </head>
    <body>
        <form method="POST">
            <?= Middlewares\Honeypot::getField() ?>
            <label>
                User:
                <input type="text" name="username">
            </label>
            <label>
                Password:
                <input type="password" name="password">
            </label>
        </form>
    </body>
</html>
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/honeypot.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/honeypot/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/honeypot.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/honeypot.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/27e336bd-2e22-4125-af7c-3a8ef44468b0.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/honeypot
[link-travis]: https://travis-ci.org/middlewares/honeypot
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/honeypot
[link-downloads]: https://packagist.org/packages/middlewares/honeypot
[link-sensiolabs]: https://insight.sensiolabs.com/projects/27e336bd-2e22-4125-af7c-3a8ef44468b0
