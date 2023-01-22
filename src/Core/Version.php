<?php

namespace PrestaShop\PrestaShop\Core;

/**
 * This class contains the current prestashop version constants.
 * This will be updated everytime we release a new version.
 */
final class Version
{
    public const VERSION = '8.1.0';
    public const MAJOR_VERSION_STRING = '8';
    public const MAJOR_VERSION = 8;
    public const MINOR_VERSION = 1;
    public const RELEASE_VERSION = 0;

    // This class should not be instanciated
    private function __construct()
    {
    }
}
