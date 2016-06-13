<?php
namespace BretRZaun\ConfigProvider\Tests;

use BretRZaun\ConfigProvider\ConfigServiceProvider;

/**
 * TestCase for ConfigServiceProvider
 *
 */
class ConfigServiceProviderTest extends \Silex\WebTestCase
{

    /**
     * create test application
     *config.yml
     * @return \Silex\Application
     */
    public function createApplication()
    {
        $app = new \Silex\Application;
        $app['debug'] = true;
        return $app;
    }

    /**
     * test loading config
     */
    public function testConfig()
    {
        $this->app->register(new ConfigServiceProvider(__DIR__.'/data/config.yml'));

        $this->assertTrue($this->app['debug']);
        $this->assertFalse($this->app['maintenance']);
        $this->assertEquals(
            [
                '/impressum' => '/content/item/123',
                '/bar' => '/foo'
            ],
            $this->app['redirects']
        );

        $this->assertEquals(
            [
                'url' => '',
                'site_id' => 1,
                'enabled' => false
            ],
            $this->app['options']
        );
    }

    /**
     * testet die Ersetzung von Platzhaltern
     */
    public function testReplacements()
    {
        $this->app->register(new ConfigServiceProvider(
            __DIR__.'/data/config.yml',
            [
                'application_path' => '/app'
            ]
        ));

        $this->assertEquals('/app/test/', $this->app['path']);
    }

    /**
     * test RuntimeException when config file not found
     */
    public function testNotExistingConfigFile()
    {
        $this->expectException('RuntimeException');
        $this->app->register(new ConfigServiceProvider(__DIR__.'/../../data/foo.yml'));
    }
}
