<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// instanciated to allow call of Legacy classes inside Symfony engine (phpunit tests, console, etc...)
require_once(__DIR__.'/../config/defines.inc.php');
require_once(__DIR__.'/../classes/PrestaShopAutoload.php');

return $loader;
