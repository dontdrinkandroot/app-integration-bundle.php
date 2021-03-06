<?php

namespace Dontdrinkandroot\AppIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ddr_app_integration');

        // @formatter:off
        $rootNode->children()
            ->scalarNode('base_href')->defaultValue('http://localhost:8000/')->end()
            ->scalarNode('angular_directory')->defaultValue('%kernel.root_dir%/../angular/')->end()
            ->scalarNode('angular_src_directory')->defaultValue('%kernel.root_dir%/../angular/src/')->end()
            ->scalarNode('angular_path')->defaultValue('app/')->end()
            ->scalarNode('api_path')->defaultValue('api/')->end()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('short_name')->isRequired()->end()
            ->scalarNode('theme_color')->defaultValue('#3f51b5')->end()
            ->scalarNode('background_color')->defaultValue('#3f51b5')->end()
            ->scalarNode('package_manager')->defaultValue('yarn')->end()
            ->arrayNode('external_styles')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('api_platform')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('operation_context_builder')->defaultFalse()->end()
                    ->booleanNode('access_control_subscriber')->defaultFalse()->end()
                ->end()
            ->end()
        ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
