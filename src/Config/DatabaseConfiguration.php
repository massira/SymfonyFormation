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
            ->end();

        $rootNode = $this->getEnumNodeConfig($rootNode);

        $rootNode = $this->getArrayNodeConfig($rootNode);

        $rootNode = $this->getArrayNodeConfigPrototype($rootNode);

        $rootNode = $this->getPrototypedArray($rootNode);

        $rootNode = $this->getConfigCasted($rootNode);

        $treeBuilder->buildTree();

        return $treeBuilder;
    }

    /**
     * Gets Enum Configs
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getEnumNodeConfig(ArrayNodeDefinition $root)
    {
        //Enum Nodes => Enum nodes provide a constraint to match the given input against a set of values
        $root->children()
             ->enumNode('delivery')
                ->values(['standard', 'expedited', 'priority'])
             ->end();

        return $root;
    }

    /**
     * Gets Array node definition
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getArrayNodeConfig(ArrayNodeDefinition $root)
    {
        $root->children()
            ->arrayNode('connection')
                ->children()
                    ->scalarNode('driver')->end()
                    ->scalarNode('host')->end()
                    ->scalarNode('username')->end()
                    ->scalarNode('password')->end()
                ->end()
            ->end();

        return $root;
    }

    /**
     * Gets array node config prototype
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getArrayNodeConfigPrototype(ArrayNodeDefinition $root)
    {
        /*
         * -A prototype can be used to add a definition which may be repeated many times inside the current node.
         * -According to the prototype definition in the example below, it is possible to have multiple connection
         *  arrays (containing a driver, host, etc.).
         */

        /*
         *-Ex YAML config
         * connections:
         *  - {driver : '', host : '', username : '', password : ''}
         *  - {driver : '', host : '', username : '', password : ''}
         *
         * -Ex XML config:
         * <connection driver='' host=''  username='' password='' />
         * <connection driver='' host=''  username='' password='' />
         *
         * -After processing The config:
         * Array(
         *  [0] => Array(
         *      'driver'   => '',
         *      'host'     => '',
         *      'username' => '',
         *      'password' => ''
         *  ),
         *  [1] => Array(
         *      'driver'   => '',
         *      'host'     => '',
         *      'username' => '',
         *      'password' => ''
         *  )
         * )
         */

        /*
         * ->Given The config tree:
         *  connections:
                sf_connection:
                    driver: value
                    host: value
                    username: value
                    password: value
                default:
                    driver: value
                    host: value
                    username: value
                    password: value
         *
         * -The processing of configuration will be the same as above(keys sf_connection and default will be lost) => The reason
         * is that the Symfony Config component treats arrays as lists by default.
         *
         * ->NOTE:
         * -If only one file provides the configuration in question, the keys (i.e. sf_connection and default) are not lost.
         * But if more than one file provides the configuration, the keys are lost as described above.
         */

        $root
            ->fixXmlConfig('connection')
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

        return $root;
    }

    /**
     * Gets prototyped array where maintaining the keys
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getPrototypedArrayWithKeys(ArrayNodeDefinition $root)
    {
        /*
         * -Ex YAML config:
         *  connections:
                sf_connection:
                    driver: value
                    host: value
                    username: value
                    password: value
                default:
                    driver: value
                    host: value
                    username: value
                    password: value
         *
         * -EX XML config:
         * <connection name='sf_connection' driver='' host=''  username='' password='' />
         * <connection name='default' driver='' host=''  username='' password='' />
         *
         * -After processing configuration:
         *  Array(
         *  [sf_connection] => Array(
         *      'driver'   => '',
         *      'host'     => '',
         *      'username' => '',
         *      'password' => ''
         *  ),
         *  [default] => Array(
         *      'driver'   => '',
         *      'host'     => '',
         *      'username' => '',
         *      'password' => ''
         *  )
         * )
         */

        $root
            ->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('driver')->end()
                            ->scalarNode('host')->end()
                            ->scalarNode('username')->end()
                            ->scalarNode('password')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $root;
    }

    /**
     * Gets Configuration string casted to array
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getConfigCasted(ArrayNodeDefinition $root)
    {
        /*
         * - $expBuilder->castToArray() : was added in symfony 3.3
         * - Sometimes, to improve the user experience of your application or bundle, you may allow to use a simple
         *   string or numeric value where an array value is required.
         * - Use the castToArray() helper to turn those variables into arrays
         */
        $root
            ->children()
                ->arrayNode('hosts')
                    ->beforeNormalization()->castToArray()
                ->end()
            ->end();

        return $root;
    }

    /**
     * Gets prototyped array
     *
     * @param ArrayNodeDefinition $root
     *
     * @return ArrayNodeDefinition
     */
    private function getPrototypedArray(ArrayNodeDefinition $root)
    {
        /*
         * -Ex YAML config
         * drivers : ['mysql', 'sqlite']
         *
         * -Ex XML config
         * <driver>mysql</driver>
         * <driver>sqlite</driver>
         *
         * -After Processing Config:
         * Array(
         *  [0] => 'mysql',
         *  [1] => 'sqlite'
         * )
         */

        $root
            ->fixXmlConfig('driver')
            ->children()
                ->arrayNode('drivers')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $root;
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





















