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
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['orm.em.configuration'] = function() use($pimple) {
            $config = new Configuration();
            if (!empty($pimple['orm.cache.class']) && $pimple['orm.cache.class'] instanceof CacheProvider) {
                $config->setMetadataCacheImpl($pimple['orm.cache.class']);
                $config->setQueryCacheImpl($pimple['orm.cache.class']);
            }

            $useSimpleAnnotationReader = $pimple['orm.simple.reader'] ? false : true;
            $driver = $config->newDefaultAnnotationDriver([$pimple['orm.path.entities']], $useSimpleAnnotationReader);

            if (self::XML_DRIVER == $pimple['orm.driver']) {
                $driver = new XmlDriver($pimple['orm.path.entities']);
            }

            if (self::YAML_DRIVER == $pimple['orm.driver']) {
                $driver = new YamlDriver($pimple['orm.path.entities']);
            }

            $config->setMetadataDriverImpl($driver);
            $config->setProxyDir($pimple['orm.proxy.directory']);
            $config->setProxyNamespace($pimple['orm.proxy.namespace']);
            $config->setAutoGenerateProxyClasses(false);

            if (isset($pimple['orm.generate.proxy.class.automatic']) &&
                is_bool($pimple['orm.generate.proxy.class.automatic'])) {
                $config->setAutoGenerateProxyClasses($pimple['orm.generate.proxy.class.automatic']);
            }

            return $config;
        };
    }
}
