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
                //boolean node
                ->booleanNode('auto_connect') //Pass the name of the node
                    ->defaultTrue()
                ->end() //Returns back to the parent node
                //scalar node
                ->scalarNode('default_connection')
                    ->defaultValue('default')
                ->end()
                //Numeric(integer|float) Nodes
                ->integerNode('positive_value')
                    ->min(0)
                ->end()
                ->floatNode('big_value')
                    ->max(5E43)
                ->end()
                ->integerNode('value_inside_a_range')
                    ->min(-50)->max(50)
                ->end()
                //Enum Nodes => Enum nodes provide a constraint to match the given input against a set of values
                ->enumNode('delivery')
                    ->values(['standard', 'expedited', 'priority'])
                ->end()
                //Array nodes
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('driver')->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
            ->end();

            /*
             * -A prototype can be used to add a definition which may be repeated many times inside the current node.
             * -According to the prototype definition in the example below, it is possible to have multiple connection
             *  arrays (containing a driver, host, etc.).
             */
            $rootNode
                ->children()
                    ->arrayNode('connections')
                        ->prototype('array') //Normally prototype('array') return ArrayNodeDefinition
                            ->children()
                                ->scalarNode('driver')->end()
                                ->scalarNode('host')->end()
                                ->scalarNode('username')->end()
                                ->scalarNode('password')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

            /*
             * - $expBuilder->castToArray() : was added in symfony 3.3
             * - Sometimes, to improve the user experience of your application or bundle, you may allow to use a simple
             *   string or numeric value where an array value is required.
             * - Use the castToArray() helper to turn those variables into arrays
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

//Nodes are created using "$nodeBuilder->node($name, $type);" or "$nodeBuilder->xxxNode($name);"





















