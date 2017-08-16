<?php

namespace Config\Formation\Controller;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Class ConfigCacheController
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
    private $rootPath;

    /**
     * ConfigCacheController constructor.
     *
     * @param LoaderInterface $loader
     * @param string          $cachePath
     * @param string          $rootPath
     */
    public function __construct(LoaderInterface $loader, $cachePath, $rootPath)
    {
        $this->loader    = $loader;
        $this->cachePath = $cachePath;
        $this->rootPath  = $rootPath;
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
            $this->rootPath.'/src/Resources/Config/file_1.yml',
            $this->rootPath.'/src/Resources/Config/file_2.yml'
        ];
    }
}
