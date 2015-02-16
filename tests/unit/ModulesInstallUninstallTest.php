<?php

namespace PrestaShop\PrestaShop\Tests\Unit;

use PHPUnit_Framework_TestCase;

use Module;
use Context;
use Employee;

class ModulesInstallUninstallTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Module::updateTranslationsAfterInstall(false);
        Context::getContext()->employee = new Employee();
        Context::getContext()->employee->id = 1;
        Context::getContext()->employee->id_profile = _PS_ADMIN_PROFILE_;
    }

    public function listModulesOnDisk()
    {
        $modules = array();

        foreach (scandir(_PS_MODULE_DIR_) as $entry)
        {
            if ($entry[0] !== '.')
            {
                if (file_exists(_PS_MODULE_DIR_.$entry.DIRECTORY_SEPARATOR.$entry.'.php'))
                {
                    $modules[] = array($entry);
                }
            }
        }

        return $modules;
    }

    /**
     * @dataProvider listModulesOnDisk
     */
    public function testInstallationAndUnInstallation($moduleName)
    {
        $module = Module::getInstanceByName($moduleName);
        if ($module->id)
        {
            $this->assertTrue((bool)$module->uninstall());
            $this->assertTrue((bool)$module->install());
        }
        else
        {
            $this->assertTrue((bool)$module->install());
            $this->assertTrue((bool)$module->uninstall());
        }
    }
}
