<?php

$parameters = require(__DIR__  . '/parameters.php');

if (!array_key_exists('parameters', $parameters)) {
    throw new \Exception('Missing "parameters" key in "parameters.php" configuration file');
}

foreach ($parameters['parameters'] as $key => $value) {
    $container->setParameter($key, $value);
}