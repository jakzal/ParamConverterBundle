<?php

namespace Zalas\Bundle\ParamConverterBundle\Tests\Integration;

use AppBundle\Site\VisitorRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceParamConverterTest extends WebTestCase
{
    public function testSuccessfulConversion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello/Kuba');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('Hello Kuba', $crawler->text());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNonSuccessfulConversion()
    {
        $client = static::createClient();

        $client->request('GET', sprintf('/hello/%s', VisitorRepository::NOT_SUPPORTED_NAME));
    }
}