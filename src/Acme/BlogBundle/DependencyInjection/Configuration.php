<?php

namespace Acme\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('acme_blog');

        $rootNode
            ->children()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue('doctrine_orm')
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifNotInArray(array('doctrine_orm', 'doctrine_mongodb'))
                                ->thenInvalid('Invalid type %s.')
                            ->end()
                        ->end()
                        ->scalarNode('manager_name')
                            ->defaultValue('default')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
