<?php

namespace Config\Formation\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class DatabaseConfiguration
 *
 * @package Config\Formation\Config
 */
class DatabaseConfiguration implements ConfigurationInterface
{
    private $treeBuilder;

    /**
     * DatabaseConfiguration constructor.
     */
    public function __construct()
    {
        $this->treeBuilder = new TreeBuilder();
    }

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $root = $this->treeBuilder->root('database');
        //Create the config tree
        $root->children()
                 ->arrayNode('connection')
                 ->isRequired()
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('driver')->isRequired()->end()
                        ->scalarNode('host')->isRequired()->end()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                 ->end()
             ->end();

        return $this->treeBuilder;
    }
}