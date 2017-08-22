<?php

namespace Config\Formation\Controller;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigTreeBuilderController
 *
 * @package Config\Formation\Controller
 */
class ConfigTreeBuilderController
{
    private $configuration;
    private $processor;
    private $configDir;

    /**
     * ConfigTreeBuilderController constructor.
     *
     * @param Processor              $processor
     * @param ConfigurationInterface $configuration
     * @param string                 $configDir
     */
    public function __construct(Processor $processor, ConfigurationInterface $configuration, $configDir)
    {
        $this->processor     = $processor;
        $this->configuration = $configuration;
        $this->configDir     = $configDir;
    }

    /**
     * Process the configuration
     */
    public function processConfiguration()
    {
        $configs          = Yaml::parse(file_get_contents($this->getConfigPath()));
        $processedConfigs = null;
        try{
            $processedConfigs = $this->processor->processConfiguration($this->configuration, $configs);
        } catch(\Exception $e){
            echo $e->getMessage();
        }


        print_r($processedConfigs);
    }

    /**
     * Gets the config path
     *
     * @return string
     */
    private function getConfigPath()
    {
        return $this->configDir.'/connection.yml';
    }
}