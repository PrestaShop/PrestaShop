<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6ff0e5a705530539c3f4f03f48949f68
{
    public static $prefixLengthsPsr4 = array (
        'P' =>
        array (
            'PrestaShop\\Module\\Banner\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PrestaShop\\Module\\Banner\\' =>
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Ps_Banner' => __DIR__ . '/../..' . '/ps_banner.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6ff0e5a705530539c3f4f03f48949f68::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6ff0e5a705530539c3f4f03f48949f68::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6ff0e5a705530539c3f4f03f48949f68::$classMap;

        }, null, ClassLoader::class);
    }
}
