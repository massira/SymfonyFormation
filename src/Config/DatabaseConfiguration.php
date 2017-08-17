<?php

namespace Config\Formation\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class DatabaseConfiguration
 *
 * @package Config\Formation\Config
 */
class DatabaseConfiguration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        //Create the Tree Builder instance
        $treeBuilder = new TreeBuilder();
        //Create root node
        /**@var ArrayNodeDefinition|NodeDefinition */
        $rootNode    = $treeBuilder->root('database');

        /**Adding Node definitions to the tree**/
        /**Variable Nodes**/
        $rootNode //The root node is an array node and has children(auto_connect and default_connection)
            ->children()
                ->booleanNode('auto_connect') //Pass the name of the node
                    ->defaultTrue()
                ->end() //Returns back to the parent node
                ->scalarNode('default_connection')
                    ->defaultValue('default')
                ->end()
            ->end();

        /*
         * - $expBuilder->castToArray() : was added in symfony 3.3
         * - Sometimes, to improve the user experience of your application or bundle, you may allow to use a simple string or numeric value
         * where an array value is required. Use the castToArray() helper to turn those variables into arrays
         */
        $rootNode
            ->children()
                ->arrayNode('hosts')
                    ->beforeNormalization()->castToArray()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

/******Node Type*******/
//It is possible to validate the type of a provided value by using the appropriate node definition.
/*
 * 1-scalar   : Generic type that includes booleans, strings, integers, floats and null
 * 2-boolean
 * 3-integer
 * 4-float
 * 5-enum     : Similar to scalar, but it only allows a finite set of values
 * 6-array
 * 7-variable : No validation
 */





















