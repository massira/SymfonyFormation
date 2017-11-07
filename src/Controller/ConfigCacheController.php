<?php

namespace Config\Formation\Controller;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Class ConfigCacheControlleryu
 *
 * @package Config\Formation\Controllers
 */
class ConfigCacheController
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $configDir;

    /**
     * ConfigCacheController constructor.
     *
     * @param LoaderInterface $loader
     * @param string          $cachePath
     * @param string          $configDir
     */
    public function __construct(LoaderInterface $loader, $cachePath, $configDir)
    {
        $this->loader    = $loader;
        $this->cachePath = $cachePath;
        $this->configDir = $configDir;
    }

    /**
     * Cache the data
     */
    public function cache()
    {
        $resources = [];
        //The second parameter is the debug mode
        $configCache = new ConfigCache($this->cachePath, true);
        //If the cache is not fresh(using timestamp and file modification time)
        if (!$configCache->isFresh()) {
            foreach ($this->getPaths() as $path) {
                //Load the resource(manipulate the config values => container)
                $this->loader->load($path);
                //For each resource we create a class metadata
                $resources[] = new FileResource($path);
            }

            //Here normally all config files are merged and validated and stored in the cache
            $code = 'Some Data';
            //Write the data into the cache, and serialize metadata classes(when debug mode is enabled)
            $configCache->write($code, $resources);
        }
    }

    /**
     * @return array
     */
    private function getPaths()
    {
        return [
            $this->configDir.'/file_1.yml',
            $this->configDir.'/file_2.yml'
        ];
    }
}
