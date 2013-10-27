<?php

namespace Sharimg\ContentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Content API Controller Tests
 */
class ApiControllerTest extends WebTestCase
{
    /**
     * Test add_content.json Method = GET
     */
    public function testAddBadMethod()
    {
        $client = static::createClient();

        $client->request('GET', '/api/add_content.json');
        
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }
    
    /**
     * Test add_content.json without param
     */
    public function testAddNoParam()
    {
        $client = static::createClient();

        $client->request('POST', '/api/add_content.json');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test add_content.json with bad params
     */
    public function testAddBadParams()
    {
        $client = static::createClient();

        $params = array(
            'description' => '',
            'from' => '',
            'is_visible' => '',
            'date' => '',
            'content_url' => '',
        );
        
        $client->request('POST', '/api/add_content.json', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test add_content.json with good params
     */
    public function testAddWithContentUrl()
    {        
        $client = static::createClient();

        $params = array(
            'description' => 'Google Logo',
            'from' => 'Google.fr',
            'is_visible' => '0',
            'date' => '',
            'content_url' => 'https://www.google.fr/images/srpr/logo11w.png',
        );
        
        $client->request('POST', '/api/add_content.json', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $content = $client->getContainer()->get('translator')->trans('api.content');
        $this->assertRegExp('/'.$content.'/', $client->getResponse()->getContent());
    }
    
    /**
     * Test add_content.json with bad params
     */
    public function testAddWithContentFile()
    {        
        $client = static::createClient();

        $params = array(
            'description' => 'Google Logo',
            'from' => 'FileUpload',
            'is_visible' => '0',
            'date' => '',
            'content_url' => '',
        );
        $contentFile = new UploadedFile('data/content.png', 'content.png', 'image/png');
        
        $client->request('POST', '/api/add_content.json', $params, array('file_input' => $contentFile));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $content = $client->getContainer()->get('translator')->trans('api.content');
        $this->assertRegExp('/'.$content.'/', $client->getResponse()->getContent());
    }
}
