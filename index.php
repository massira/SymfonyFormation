<?php

use Config\Formation\Controller\ConfigCacheController;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;
use Config\Formation\Loader\YamlFileLoader;

require 'vendor/autoload.php';

$rootDir          = __DIR__;
$configPath       = $rootDir.'/src/Config';
$locator          = new FileLocator([$configPath]);
$resolver         = new LoaderResolver([new YamlFileLoader($locator)]);
$delegatingLoader = new DelegatingLoader($resolver);
$configCacheController = new ConfigCacheController($delegatingLoader, $rootDir);
$configCacheController->cache();