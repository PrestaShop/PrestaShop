<?php
namespace PrestaShop\PrestaShop\Tests\TestCase;
use PHPUnit_Framework_TestCase;

class IntegrationTestCase extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once(__DIR__ . '/../../config/config.inc.php');
    }
}
