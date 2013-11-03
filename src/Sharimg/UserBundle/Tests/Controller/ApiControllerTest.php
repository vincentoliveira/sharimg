<?php

namespace Sharimg\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    /**
     * Test add_content.json with good params
     */
    public function testRegisterMissingParams()
    {        
        $client = static::createClient();

        $params = array(
            'email' => '',
            'username' => '',
            'password' => '',
        );
        
        $client->request('POST', '/api/register.json', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test register.json
     */
    public function testRegisterEmailBadFormat()
    {        
        $client = static::createClient();

        $params = array(
            'email' => 'testRegisterEmailBadFormat',
            'username' => 'testRegisterEmailBadFormat',
            'password' => 'testRegisterEmailBadFormat',
        );
        $client->request('POST', '/api/register.json', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test register.json
     */
    public function testRegisterEmailAlreadyUsed()
    {        
        $client = static::createClient();

        $params1 = array(
            'email' => 'testRegisterEmailAlreadyUsed@test.com',
            'username' => 'testRegisterEmailAlreadyUsed',
            'password' => 'testRegisterEmailAlreadyUsed',
        );
        $params2 = array(
            'email' => 'testRegisterEmailAlreadyUsed@test.com',
            'username' => 'testRegisterEmailAlreadyUsed2',
            'password' => 'testRegisterEmailAlreadyUsed2',
        );
        $client->request('POST', '/api/register.json', $params1);
        $client->request('POST', '/api/register.json', $params2);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test register.json
     */
    public function testRegisterUsernameAlreadyUsed()
    {        
        $client = static::createClient();

        $params1 = array(
            'email' => 'testRegisterUsernameAlreadyUsed@test.com',
            'username' => 'testRegisterUsernameAlreadyUsed',
            'password' => 'testRegisterUsernameAlreadyUsed',
        );
        $params2 = array(
            'email' => 'testRegisterUsernameAlreadyUsed2@test.com',
            'username' => 'testRegisterUsernameAlreadyUsed',
            'password' => 'testRegisterUsernameAlreadyUsed',
        );
        $client->request('POST', '/api/register.json', $params1);
        $client->request('POST', '/api/register.json', $params2);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->assertRegExp('/error/', $client->getResponse()->getContent());
    }
    
    /**
     * Test register.json
     */
    public function testRegisterSuccess()
    {        
        $client = static::createClient();

        $params = array(
            'email' => 'testRegisterSuccess@test.com',
            'username' => 'testRegisterSuccess',
            'password' => 'testRegisterSuccess',
        );
        
        $client->request('POST', '/api/register.json', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $this->assertRegExp('/register/', $client->getResponse()->getContent());
    }
}
