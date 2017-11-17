<?php

require_once 'Exception/BuildException.php';
require_once 'Library/ReleaseCreator.php';

$options = getopt('', ['version:']);

if (empty($options['version'])) {
    echo "\e[31mERROR:\n";
    echo "'version' option missing.\n";
    echo "\e[32mExample:\n";
    echo "'php CreateRelease.php --version='1.7.2.4'\e[0m\n";
    exit(1);
}

try {
    $releaseCreator = new ReleaseCreator($options['version']);
    $releaseCreator->createRelease();
} catch (Exception $e) {
    echo "\e[31mERROR:\n";
    echo "Can not create the release.\n";
    echo "{$e->getMessage()}\e[0m\n";
    exit(1);
}
exit(0);