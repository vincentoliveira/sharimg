<?php

namespace Sharimg\ContentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/helloworld');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/Hello world!/', $client->getResponse()->getContent());
    }
}
