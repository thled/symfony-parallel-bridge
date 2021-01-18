<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PublicplanParallelBridgeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $poolFactoryDefinition = $container->getDefinition('publicplan_parallel_bridge.factory.pool_factory');
        $poolFactoryDefinition->setArgument(0, $config['project_dir']);
    }
}
