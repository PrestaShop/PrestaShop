<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Classes\module;

use Cache;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ModuleTest extends TestCase
{
    /**
     * @return array a list of modules to control override features
     */
    public function providerModulesOnDisk(): array
    {
        return [
            ['bankwire'],
            ['cronjobs'],
            ['ganalytics'],
            ['ps_emailsubscription'],
            ['ps_featuredproducts'],
        ];
    }

    /**
     * @dataProvider providerModulesOnDisk
     * Note: improves module list fixtures in order to cancel any override.
     *
     * @param string $moduleName the module name
     */
    public function testDummyGetOverride(string $moduleName): void
    {
        $module = Module::getInstanceByName($moduleName);

        $this->assertNotFalse($module);
        $this->assertInstanceOf(Module::class, $module);
        $this->assertEmpty($module->getOverrides());
    }

    public function testRealOverrideInModuleDir(): void
    {
        HelperModule::addModule('pscsx3241');
        $module = Module::getInstanceByName('pscsx3241');
        $overrides = $module->getOverrides();

        $this->assertContains('Cart', $overrides);
        $this->assertContains('AdminProductsController', $overrides);
        $this->assertCount(2, $overrides);

        HelperModule::removeModule('pscsx3241');
    }

    /**
     * Test if a module return the good possible hooks list.
     * This test is done on the bankwire generic module.
     *
     * Note: improves module list fixtures in order to get an explicit list of hooks.
     */
    public function testGetRightListForModule(): void
    {
        define('STDIN', true);
        ModuleManagerBuilder::getInstance()->build()->install('bankwire');
        $module = Module::getInstanceByName('bankwire');
        Cache::clean('hook_alias');
        $possibleHooksList = $module->getPossibleHooksList();

        $this->assertCount(2, $possibleHooksList);

        $this->assertEquals('displayPaymentReturn', $possibleHooksList[0]['name']);
        $this->assertEquals('paymentOptions', $possibleHooksList[1]['name']);

        Module::getInstanceByName('bankwire')->uninstall();
    }

    public function testCacheBehaviour(): void
    {
        Module::deleteTrustedXmlCache();
        Module::getModulesOnDisk();
        $trustedFileCreationTime = filemtime(_PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml');
        sleep(1);
        clearstatcache();
        Module::getModulesOnDisk();
        $newTrustedFileCreationTime = filemtime(_PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml');

        // make sure the cache files are not regenerated
        // (same timestamp on the cache file between two subsequent call to getModulesOnDisk)
        $this->assertEquals($trustedFileCreationTime, $newTrustedFileCreationTime);
    }
}

define('_RESSOURCE_MODULE_DIR_', realpath(dirname(__FILE__, 4) . '/Resources/modules_tests/'));

class HelperModule
{
    /**
     * Copy the directory in resources which get the name $module_dir_name in the module directory
     *
     * @var string module_dir_name take the directory name of a module contain in /home/prestashop/tests/resources/module
     */
    public static function addModule(string $module_dir_name): bool
    {
        if (is_dir(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseCopy(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name, _PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Delete the directory in /home/prestashop/module which get the name $module_dir_name
     *
     * @var string module_dir_name take the directory name of a module contain in /home/prestashop/module
     */
    public static function removeModule(string $module_dir_name): bool
    {
        if (is_dir(_PS_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseDelete(_PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Recursivly copy a directory
     *
     * @var the source path (eg. /home/dir/to/copy)
     * @var the destination path (eg. /home/)
     */
    private static function recurseCopy(string $src, string $dst): void
    {
        $dirp = opendir($src);
        @mkdir($dst);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    static::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
    }

    /**
     * Recursivly delete a directory
     *
     * @var the directory to delete path (eg. /home/dir/to/delete)
     */
    private static function recurseDelete(string $dir): void
    {
        $dirp = opendir($dir);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . '/' . $file)) {
                    static::recurseDelete($dir . '/' . $file);
                } else {
                    unlink($dir . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
        rmdir($dir);
    }
}
