# Larafun Middleware

[![Build Status](https://travis-ci.org/larafun/middleware.svg?branch=master)](https://travis-ci.org/larafun/middleware)
[![Latest Stable Version](https://poser.pugx.org/larafun/middleware/v/stable)](https://packagist.org/packages/larafun/middleware)
[![License](https://poser.pugx.org/larafun/middleware/license)](https://packagist.org/packages/larafun/middleware)
[![Total Downloads](https://poser.pugx.org/larafun/middleware/downloads)](https://packagist.org/packages/larafun/middleware)

On various occasions Laravel will check if the `Request` `wantsJson()` to determine the type of `Response` it needs to build. If the check does not pass, Laravel might return with redirects (in case of `ValidationException`) or with some pretty Blade templates (in case of `404 Responses`). These are all great features, but when you use Laravel to build your JSON APIs, you might want to respond with JSON messages when your API endpoints are being called.

This package offers an `AcceptJsonMiddleware` that will add the `application/json` Accept Header to all your requests, so that the `Request::wantsJson()` method will validate it. This middleware does not overwrite the existing media types present in the Accept Header, but in case of equal qualities will make sure that the `Request::wantsJson()` will pass.

## Installation

Requires PHP > 7.0, Laravel > 5.5

```bash
composer require larafun/middleware
```

## Basic Usage

Just add the `AcceptJsonMiddleware` to your `App\Http\Kernel` class:

```php
class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        // ...
        'api'   => [
            'accept-json',
            'throttle:60,1',
            'bindings'
        ]
    ];

    protected $routeMiddleware = [
        // ...
        'accept-json'   => \Larafun\Middleware\AcceptJsonMiddleware::class,
    ];
}
```

## 404 Not Found

Since the 404 message is triggered when no routes have been matched with the requested URL, placing the Middleware in a Route group will not apply it. If you also want your 404 messages to be handled as JSON, you can either use the [Laravel Fallback Routes](https://laravel.com/docs/5.8/routing#fallback-routes), or place the Middleware as the first item in the `$middleware` property of your `Kernel`.

```php
class Kernel extends HttpKernel
{
    protected $middleware = [
        \Larafun\Middleware\AcceptJsonMiddleware::class,
        // ...
    ];
}
```

## Changing Quality

In the rare case you might want a different quality for your `application/json` header, just pass it as a parameter in your `Kernel`.

```php
class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        // ...
        'api'   => [
            'accept-json:0.8',
            //...
        ]
    ];
}
```

You should be aware that HTTP server configuration might add some default Accept Headers with quality 1. In this case, your settings will never take effect if you provide a lower quality. For best results you stick with the default quality setting.

## Existing Header

If the request already has an `application/json` Accept Header this Middleware will not overwrite it and maintain the consumer request quality.

You can force your new quality, passing the `force` string as the second parameter.

```php
class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        // ...
        'api'   => [
            'accept-json:1,force',
            //...
        ]
    ];
}
```
