<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'Phalcon' => BASE_PATH . '/vendor/phalcon/incubator/Library/Phalcon/',
    'Dropbox' => BASE_PATH . '/vendor/dropbox/dropbox-sdk/lib/Dropbox/'
]);
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->formsDir,
    ]
)->register();
