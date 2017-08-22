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
class DatabaseConfiguration_2 implements ConfigurationInterface
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
                ->append($this->getEnumNodeConfig()) //Append section of definition
                ->append($this->getArrayNodeConfig())
                ->append($this->getArrayNodeConfigPrototype())
                ->append($this->getPrototypedArray())
                ->append($this->getPrototypedArrayWithKeys())
                ->append($this->getConfigCasted())
                ->append($this->getConfigurationWithNormalization())
            ->end();

        return $treeBuilder;
    }

    /**
     * Gets Enum Configs
     *
     * @return NodeDefinition
     */
    private function getEnumNodeConfig()
    {
        //Enum Nodes => Enum nodes provide a constraint to match the given input against a set of values
        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('enum');
        $root->children()
             ->enumNode('delivery')
                ->values(['standard', 'expedited', 'priority'])
             ->end();

        return $root;
    }

    /**
     * Gets Array node definition
     *
     * @return NodeDefinition
     */
    private function getArrayNodeConfig()
    {
        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('array_node_config');
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
     * @return NodeDefinition
     */
    private function getArrayNodeConfigPrototype()
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

        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('array_node_config_prototype');
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
     * @return NodeDefinition
     */
    private function getPrototypedArrayWithKeys()
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
         * -Ex XML config:
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

        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('array_node_config_with_keys');
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
     * @return NodeDefinition
     */
    private function getConfigCasted()
    {
        /*
         * - $expBuilder->castToArray() : was added in symfony 3.3
         * - Sometimes, to improve the user experience of your application or bundle, you may allow to use a simple
         *   string or numeric value where an array value is required.
         * - Use the castToArray() helper to turn those variables into arrays
         */
        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('array_node_casted');
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
     * @return NodeDefinition
     */
    private function getPrototypedArray()
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

        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('prototyped_array_node');
        $root
            ->fixXmlConfig('driver')
                ->arrayNode('drivers')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $root;
    }

    /**
     * Gets the configuration and controlling the normalization process
     *
     * Control the normalization process
     *
     * @return NodeDefinition
     */
    private function getConfigurationWithNormalization()
    {
        /*
         * ->This two configuration will be accepted:
         * ->YAML_1
         * connection:
         *     name: mysql_driver_connection
         *     host: ~
         *     driver:~
         *     username:~
         *     password:~
         *
         * ->YAML_2
         * connection: mysql_driver_connection
         */

        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('connection_normalized');
        $root->children()
                ->arrayNode('connections')
                    ->beforeNormalization()  //returns a NormalizationBuilder or a ExprBuilder instance
                        ->ifString()
                        //Changing a string value into an associative array with name as the key
                        ->then(function($v){ return ['name' => $v]; })
                    ->end()
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('driver')->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end();

        return $root;
    }

    /**
     * @return NodeDefinition
     */
    private function getConfigurationWithAdvancedValidation()
    {
        /*
         * ->The builder(ExprBuilder) is used for adding advanced validation rules to node definitions.
         * ->A validation rule is defined by two parts:
         *   -"if" part:
         *      ifTrue()
                ifString()
                ifNull()
                ifEmpty() (since Symfony 3.2)
                ifArray()
                ifInArray()
                ifNotInArray()
                always()
         *
         *   -"then" part:
         *      then()
                thenEmptyArray()
                thenInvalid()
                thenUnset()
         * ->'Usually, "then" is a closure'. Its return value will be used as a new value for the node, instead of the node's
         * original value.
         */

        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('connection_validation_rules');
        $root->children()
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('driver')
                            ->isRequired()
                            ->validate() //using ExpBuilder
                            ->ifInArray(['mysql', 'sqlite', 'mssql'])
                                ->thenInvalid('Invalid database driver %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
             ->end();

        return $root;
    }
}

