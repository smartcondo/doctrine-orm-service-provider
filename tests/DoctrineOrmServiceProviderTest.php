<?php

/**
 * Class DoctrineServiceProviderTest
 */
class DoctrineServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    protected $provider;

    public function testInstance()
    {
        $this->provider = new \SmartCondo\DoctrineOrmServiceProvider();

        $this->assertInstanceOf('\Pimple\ServiceProviderInterface', $this->provider);
    }

    public function testRegister()
    {
        $app = new \Pimple\Container();
        $app->register(new \SmartCondo\DoctrineOrmServiceProvider());

        $this->assertTrue(isset($app['orm.em.configuration']));
    }
}
