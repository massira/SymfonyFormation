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
    const CACHE_PATH = '/var/www/symfony_formation/cache/appUserMatcher.php';

    /**
     * ConfigCacheController constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Cache the data
     */
    public function cache()
    {
        $resources = [];
        $configCache = new ConfigCache(self::CACHE_PATH, true);
        //If the cache is not fresh
        if (!$configCache->isFresh()) {
            foreach ($this->getPaths() as $path) {
                $this->loader->load($path);
                $resources[] = new FileResource($path);
            }

            $code = 'Some Data 2';
            echo self::CACHE_PATH;
            $configCache->write($code, $resources);
            echo 'Success';
        }
    }

    /**
     * @return array
     */
    private function getPaths()
    {
        return [
            '/var/www/symfony_formation/src/Config/file_1.yml',
            '/var/www/symfony_formation/src/Config/file_2.yml'
        ];
    }
}