<?php

use Config\Formation\Controller\ConfigCacheController;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;
use Config\Formation\Loader\YamlFileLoader;
use Config\Formation\Config\DatabaseConfiguration;
use Config\Formation\Controller\ConfigTreeBuilderController;
use Symfony\Component\Config\Definition\Processor;

require 'vendor/autoload.php';

$rootDir               = __DIR__;
//Is where the File Locator will search for resources(when loading resource)
$configPath            = $rootDir.'/src/Config/Resources';

/*
//The cache path where to store the config and metadata(used by the ConfigCache class)
$cachePath             = $rootDir.'/cache/appUserMatcher.php';
//Create a new File Locator instance and pass an array of paths to search in
$locator               = new FileLocator([$configPath]);
//Create a new Loader instance and pass the file locator instance
$loader                = new YamlFileLoader($locator);
//Create a new Resolver instance and pass an array of loaders
$resolver              = new LoaderResolver([$loader]);
//Create a new Delegating Loader instance and pass the resolver as constructor parameter
$delegatingLoader      = new DelegatingLoader($resolver);
//Create a new ConfigCacheController instance
$configCacheController = new ConfigCacheController($loader, $cachePath, $configPath);
//Cache the configuration
$configCacheController->cache();
*/

//Processing the configuration
$databaseConfiguration       = new DatabaseConfiguration();
$processor                   = new Processor();
$configTreeBuilderController = new ConfigTreeBuilderController($processor, $databaseConfiguration, $configPath);
$configTreeBuilderController->processConfiguration();
