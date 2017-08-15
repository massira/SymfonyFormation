<?php

namespace Config\Formation\Loader;

use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class YamlFileLoader
 *
 * @package Config
 */
class YamlFileLoader extends FileLoader
{
    /**
     * Load resource and work with its content(when the cache is not fresh)
     *
     * @param string       $resource
     * @param null|string  $type
     */
    public function load($resource, $type = null)
    {
        $configValues = file_get_contents($resource);

        //... handle the config values

        //Adding the config values to the container(setParameter)

        // maybe import some other resource:

        // $this->import('extra_users.yml');
    }

    /**
     * @param string       $resource
     * @param null|string  $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}