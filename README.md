OhHttp
======

OhHttp is a HTTP Request and Response library for PHP

[![Latest Stable Version](https://poser.pugx.org/rogerthomas84/ohhttp/v/stable.svg)](https://packagist.org/packages/rogerthomas84/ohhttp)
[![Total Downloads](https://poser.pugx.org/rogerthomas84/ohhttp/downloads.svg)](https://packagist.org/packages/rogerthomas84/ohhttp)
[![Latest Unstable Version](https://poser.pugx.org/rogerthomas84/ohhttp/v/unstable.svg)](https://packagist.org/packages/rogerthomas84/ohhttp)
[![License](https://poser.pugx.org/rogerthomas84/ohhttp/license.svg)](https://packagist.org/packages/rogerthomas84/ohhttp)
[![Build Status](https://travis-ci.org/rogerthomas84/ohhttp.png)](http://travis-ci.org/rogerthomas84/ohhttp)

Using Composer
--------------

To use OhHttp with Composer, add the dependency (and version constraint) to your require block inside your `composer.json` file.

```json
{
    "require": {
        "rogerthomas84/ohhttp": "1.0.*"
    }
}
```

Quick Start
-----------

You can use the `\OhHttp\Request` and `\OhHttp\Response` to provide a simplified HTTP Request and Response, but moving
towards a more OO approach.

## Request

```php
$request = new \OhHttp\Request();
if ($request->isGet()) {
    $userId = $request->getParam('id', null);
    // do something
} elseif ($request->isPost()) {
    $userId = $request->getParam('id', null);
    // do something
}
```

## Response

```php
$response = new \OhHttp\Response();
$response->setHeader('Content-Type', 'application/json');
$response->setBody('{"foo":"bar"}');
$response->send();
```
