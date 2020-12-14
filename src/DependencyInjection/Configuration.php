<?php

declare(strict_types=1);

namespace Publicplan\ParallelBridge\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('publicplan_parallel_bridge');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        /** @phpstan-ignore-next-line */
        $rootNode->children()
                 ->integerNode('amphp_max_worker')
                    ->defaultValue(3)
                    ->info('Amount of workers that can run in parallel')
                    ->end()
                 ->scalarNode('project_dir')
                    ->defaultValue('%kernel.project_dir%')
                    ->info('we need your projects home dir to locate the worker-bootstrap.php')
                    ->end()
                 ->end();

        return $treeBuilder;
    }
}
