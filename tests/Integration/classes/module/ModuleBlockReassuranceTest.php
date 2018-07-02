<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration;

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Tests\TestCase\IntegrationTestCase;
use Module;

/**
 * Test that module blockreassurance behavior is correct
 *
 * Is tested:
 * - install process
 * - uninstall process
 * Tests to add:
 * - rendering of the widget (cannot be tested now, it requires proper Smarty initialization)
 * - rendering of thr admin form (cannot be tested now, it requires proper Smarty initialization)
 */
class ModuleBlockReassuranceTest extends IntegrationTestCase
{
    /**
     * Flag used to control whether the module should be uninstalled at tear down after a test
     *
     * @var bool
     */
    private static $shouldUninstallAtTearDown = true;

    protected function setUp()
    {
        $moduleLocation = _PS_MODULE_DIR_ . '/blockreassurance';

        $doesModuleExistIntoTestModuleDirectory = ((file_exists($moduleLocation)) && (is_dir($moduleLocation)));

        // copy module from project into tests
        if (false === $doesModuleExistIntoTestModuleDirectory) {
            $expectedModuleLocation = _PS_ROOT_DIR_ . '/modules/blockreassurance';
            $doesModuleExistIntoPSModuleDirectory = ((file_exists($expectedModuleLocation)) && (is_dir($expectedModuleLocation)));

            if (false === $doesModuleExistIntoPSModuleDirectory) {
                self::$shouldUninstallAtTearDown = false;
                throw new \Exception('Looks like module blockreassurance is not installed. It must be for the test to run. Run $ composer install.');
            }

            $copyResult = self::copyr($expectedModuleLocation, $moduleLocation);

            if (false === $copyResult) {
                throw new \Exception('Failed to copy blockreassurance module for tests.');
            }
        }

        parent::setUp();
    }

    /**
     * Test if blockreassurance module install is successfull
     */
    public function testInstallIsSuccessfull()
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $moduleManager->install('blockreassurance');
        $module = Module::getInstanceByName('blockreassurance');
        \Cache::clean('hook_alias');

        // assert install is successfull

        self::assertTrue($moduleManager->isInstalled('blockreassurance'));
        self::assertTrue($moduleManager->isEnabled('blockreassurance'));

        self::assertTrue($this->tableExists('reassurance'));
        self::assertTrue($this->tableExists('reassurance_lang'));

        self::assertEquals(5, \Configuration::get('BLOCKREASSURANCE_NBBLOCKS'));
    }

    /**
     * Test if blockreassurance module uninstall is successfull
     */
    public function testUninstallIsSuccessfull()
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $moduleManager->install('blockreassurance');
        $module = Module::getInstanceByName('blockreassurance');

        Module::getInstanceByName('blockreassurance')->uninstall();

        // do not perform 'uninstall' at tear down
        self::$shouldUninstallAtTearDown = false;

        // assert uninstall is successfull
        self::assertFalse($moduleManager->isInstalled('blockreassurance'));
        self::assertFalse($moduleManager->isEnabled('blockreassurance'));

        self::assertFalse($this->tableExists('reassurance'));
        self::assertFalse($this->tableExists('reassurance_lang'));

        self::assertEquals(false, \Configuration::get('BLOCKREASSURANCE_NBBLOCKS'));
    }

    public static function tearDownAfterClass()
    {
        if (self::$shouldUninstallAtTearDown) {
            Module::getInstanceByName('blockreassurance')->uninstall();
        }
    }

    /**
     * @param string $tableName
     *
     * @return bool
     *
     * @throws \Exception
     *
     * @todo: move this into a Util class for tests
     */
    private function tableExists($tableName)
    {
        $query = sprintf('SELECT COUNT(*) FROM %s%s', _DB_PREFIX_, $tableName);
        try {
            \Db::getInstance()->executeS($query);
        } catch (\PrestaShopDatabaseException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $source
     * @param string $dest
     *
     * @return bool
     *
     * @todo: move this into a Util class for tests
     *
     * @see http://www.aidanlister.com/2004/04/recursively-copying-directories-in-php/
     */
    private static function copyr($source, $dest)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            self::copyr("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }
}
