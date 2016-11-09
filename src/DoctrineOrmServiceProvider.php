<?php

namespace SmartCondo;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class DoctrineServiceProvider
 * @package SmartCondo
 * @license MIT
 * @version 1.0
 * @author Vinicius V. de Oliveira <vinyvicente@gmail.com>
 */
class DoctrineOrmServiceProvider implements ServiceProviderInterface
{
    const XML_DRIVER = 'xml';
    const YAML_DRIVER = 'yaml';

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['orm.em.configuration'] = function() use($app) {
            $config = new Configuration();
            if (!empty($app['orm.cache.class']) && $app['orm.cache.class'] instanceof CacheProvider) {
                $config->setMetadataCacheImpl($app['orm.cache.class']);
                $config->setQueryCacheImpl($app['orm.cache.class']);
            }

            $useSimpleAnnotationReader = $app['simple.reader'] ? false : true;
            $driver = $config->newDefaultAnnotationDriver([$app['orm.path.entities']], $useSimpleAnnotationReader);

            if (self::XML_DRIVER == $app['orm.driver']) {
                $driver = new XmlDriver($app['orm.path.entities']);
            }

            if (self::YAML_DRIVER == $app['orm.driver']) {
                $driver = new YamlDriver($app['orm.path.entities']);
            }

            $config->setMetadataDriverImpl($driver);
            $config->setProxyDir($app['orm.proxy.directory']);
            $config->setProxyNamespace($app['orm.proxy.namespace']);
            $config->setAutoGenerateProxyClasses(false);

            if (isset($app['orm.generate.proxy.class.automatic']) &&
                is_bool($app['orm.generate.proxy.class.automatic'])) {
                $config->setAutoGenerateProxyClasses($app['orm.generate.proxy.class.automatic']);
            }

            return $config;
        };
    }
}
