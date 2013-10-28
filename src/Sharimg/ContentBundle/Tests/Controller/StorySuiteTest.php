<?php

namespace Sharimg\ContentBundle\Tests\Controller;

use Behat\Behat\Console\BehatApplication;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests BeHat
 */
class StorySuiteTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Setup
     */
    protected function setup()
    {
        $this->client = static::createClient(array('environment' => 'test'));
        $this->client->followRedirects();
    }

    /**
     * @test
     * @group Stories
     */
    public function scenariosMeetAcceptanceCriteria()
    {
        $input = new ArrayInput(array(
                    'features' => '@SharimgContentBundle'
                 ));

        $output = new ConsoleOutput();
        $app = new BehatApplication('DEV');
        $app->setAutoExit(false);

        $result = $app->run($input, $output);
        $this->assertEquals(0, $result, 'Au moins un des scénarios Behat ne passe pas dans UserBundle, pour plus d\'infos voir dans build/logs/features/UserBundle/');
    }
}
