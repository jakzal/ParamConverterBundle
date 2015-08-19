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