<?php


namespace PrestaShop\PrestaShop\Tests\TestCase;

use PHPUnit_Framework_TestCase;

class IntegrationTestCase extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once(_PS_CONFIG_DIR_ . '/config.inc.php');
    }
}
