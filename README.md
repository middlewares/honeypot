# middlewares/honeypot

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to implement a honeypot spam prevention. This technique is based on creating a input field that should be invisible and left empty by real users but filled by most spam bots. The middleware check in the incoming requests whether this value exists and is empty (is a real user) or doesn't exist or has a value (is a bot) returning a 403 response.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/honeypot](https://packagist.org/packages/middlewares/honeypot).

```sh
composer require middlewares/honeypot
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\Honeypot()
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Usage

In your forms, you have to include a `<input>` element that will be used as trap:

```html
<html>
    <head>
        <style type="text/css">
            input[name="hpt_name"] { display: none; }
        </style>
    </head>
    <body>
        <form method="POST">
            <!-- This is the honeypot -->
            <input type="text" name="hpt_name" arial-label="Please, do not fill this input">

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

The middleware by default expect the input name is `hpt_name` but you can change it. Note also the css code that hide the honeypot, so users do not see anything, only robots. You may need to add some accesibility attributes like `aria-label` for screen readers.

```php
//Check the default "htp_name" value
$honeypot = new Middlewares\Honeypot();

//Check other value, for example "nobots"
$honeypot = new Middlewares\Honeypot('nobots');
```

Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument to create the error response (`403`) when spam is detected. If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

$honeypot = new Middlewares\Honeypot('htp_name', $responseFactory);
```

## Helpers

### getField

This static method is provided to ease the creation of the input field, accepting two arguments: the input name and a label used for screen readers. If no name is provided, use the same name passed previously to the middleware.

Example:

```html
<form method="POST">
    <?= Middlewares\Honeypot::getField('htp_name', 'Please, do not fill this input') ?>
    <label>
        User:
        <input type="text" name="username">
    </label>
    <label>
        Password:
        <input type="password" name="password">
    </label>
</form>
```

### getHiddenField

This static method generates the input field just like `getField()` does, but adds inline CSS to hide the field directly. Note: This may be easier to detect for some bots.
If you want to get creative with hiding the field, use `getField()` in combination with custom CSS (or JS).

```html
<form method="POST">
    <?= Middlewares\Honeypot::getHiddenField() ?>
    <label>
        User:
        <input type="text" name="username">
    </label>
    <label>
        Password:
        <input type="password" name="password">
    </label>
</form>
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/honeypot.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/honeypot/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/honeypot.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/honeypot.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/honeypot
[link-travis]: https://travis-ci.org/middlewares/honeypot
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/honeypot
[link-downloads]: https://packagist.org/packages/middlewares/honeypot
