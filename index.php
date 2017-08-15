<?php

use Config\Formation\Controller\ConfigCacheController;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;
use Config\Formation\Loader\YamlFileLoader;

require 'vendor/autoload.php';

$configPath       = __DIR__.'/src/Config';
$locator          = new FileLocator([$configPath]);
$resolver         = new LoaderResolver([new YamlFileLoader($locator)]);
$delegatingLoader = new DelegatingLoader($resolver);
$configCacheController = new ConfigCacheController($delegatingLoader);
$configCacheController->cache();