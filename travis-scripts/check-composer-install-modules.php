<?php

$composerJsonFilepath = __DIR__ . '/../composer.json';
$composerConfiguration = json_decode(file_get_contents($composerJsonFilepath), true);
$modulesList = array_keys($composerConfiguration['extra']['prestashop']['modules']);

foreach ($modulesList as $moduleProjectName) {
    $moduleName = str_replace('prestashop/', '', $moduleProjectName);
    $expectedModuleFolderPath = __DIR__ . '/../modules/' . $moduleName;

    if (false === is_dir($expectedModuleFolderPath)) {
        throw new \Exception('Could not find module ' . $moduleName . ' directory at ' . $expectedModuleFolderPath);
    }
}

return 0;
