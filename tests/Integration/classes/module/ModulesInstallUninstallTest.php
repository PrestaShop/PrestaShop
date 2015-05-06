<?php

namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Module;
use Context;
use Employee;

class ModulesInstallUninstallTest extends IntegrationTestCase
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
     * @group slow
     */
    public function testInstallationAndUnInstallation($moduleName)
    {
        $module = Module::getInstanceByName($moduleName);
        if ($module->id)
        {
            $this->assertTrue((bool)$module->uninstall(), 'Module uninstall failed : '.$moduleName);
            $this->assertTrue((bool)$module->install(), 'Module install failed : '.$moduleName);
        }
        else
        {
            $this->assertTrue((bool)$module->install(), 'Module install failed : '.$moduleName);
            $this->assertTrue((bool)$module->uninstall(), 'Module uninstall failed : '.$moduleName);
        }
    }
}
