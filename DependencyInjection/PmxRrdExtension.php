<?php

namespace Pmx\Bundle\RrdBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PmxRrdExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $rootDir = $container->getParameter('kernel.root_dir');

        if (null === $config['database_location']) {
            $container->setParameter('pmx_rrd.database_location', $rootDir . '/rrd');
        }

        if (null === $config['graph_location']) {
            $container->setParameter('pmx_rrd.graph_location', $rootDir . '/../web/rrd');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
