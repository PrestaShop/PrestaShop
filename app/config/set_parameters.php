<?php

use PrestaShopBundle\Install\Upgrade;

$parametersFilepath = __DIR__  . '/parameters.php';
if (!file_exists($parametersFilepath)) {
    // let's check first if there's some old config files which could be migrated
    if (Upgrade::migrateSettingsFile() === false) {
        // nothing to migrate ? return
        return;
    }
}

$parameters = require($parametersFilepath);

if (!array_key_exists('parameters', $parameters)) {
    throw new \Exception('Missing "parameters" key in "parameters.php" configuration file');
}

foreach ($parameters['parameters'] as $key => $value) {
    $container->setParameter($key, $value);
}
