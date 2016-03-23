Param Converter Bundle
======================

This bundle provides additional param converters for Symfony.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jakzal/ParamConverterBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jakzal/ParamConverterBundle/?branch=master)
[![Build Status](https://travis-ci.org/jakzal/ParamConverterBundle.svg?branch=master)](https://travis-ci.org/jakzal/ParamConverterBundle)

Installation
------------

This bundle requires:

* PHP 5.5+
* sensio/framework-extra-bundle ~3.0

The easiest way to install it is to use Composer:

```
$ composer require zalas/param-converter-bundle:^1.0
```

Service Param Converter
-----------------------

The service param converter calls a configured service to convert a request
attribute to an object.

Options:

* service - a service id
* method - a method name to be called on the service
* arguments - list of request attributes to be passed as method call arguments

Example:

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Site\Visitor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DemoController
{
    /**
     * @Route("/hello/{name}")
     * @ParamConverter(
     *     "visitor",
     *     converter="service",
     *     options={
     *         "service": "visitor_repository",
     *         "method": "findByName",
     *         "arguments": {"name"}
     *     }
     * )
     */
    public function indexAction(Visitor $visitor)
    {
        return new Response('Hello '.$visitor->getName());
    }
}
```
