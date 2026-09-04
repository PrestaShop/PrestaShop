<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Addon\Module;

use Context;
use Employee;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Tests\Resources\ResourceResetter;
use Tools;

/**
 * These tests install and uninstalls modules causing the cache to be cleared. So it's better to run it isolated.
 *
 * @group isolatedProcess
 */
class ModuleManagerBuilderTest extends TestCase
{
    /**
     * @var ModuleManagerBuilder
     */
    public $moduleManagerBuilder;
    /**
     * @var ModuleManager
     */
    public $moduleManager;
    /**
     * @var string[]
     */
    public $moduleNames;
    /**
     * @var string[]
     */
    public $conflictModuleNames;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dirResources = dirname(__DIR__, 4);

        if (is_dir($dirResources . '/Resources/modules_tests/pscsx3241')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/pscsx3241', _PS_MODULE_DIR_ . '/pscsx3241');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/pscsx32412')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/pscsx32412', _PS_MODULE_DIR_ . '/pscsx32412');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testconflict', _PS_MODULE_DIR_ . '/testconflict');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testtrickyconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testtrickyconflict', _PS_MODULE_DIR_ . '/testtrickyconflict');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testpropertyconflict')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testpropertyconflict', _PS_MODULE_DIR_ . '/testpropertyconflict');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testtypedpropertyoverride')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testtypedpropertyoverride', _PS_MODULE_DIR_ . '/testtypedpropertyoverride');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testmultilinepropertyoverride')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testmultilinepropertyoverride', _PS_MODULE_DIR_ . '/testmultilinepropertyoverride');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testmodifiersoverride')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testmodifiersoverride', _PS_MODULE_DIR_ . '/testmodifiersoverride');
        }
        if (is_dir($dirResources . '/Resources/modules_tests/testmoduleclassoverride')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/testmoduleclassoverride', _PS_MODULE_DIR_ . '/testmoduleclassoverride');
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        // Uninstall modules
        if (Module::isInstalled('pscsx3241')) {
            Module::getInstanceByName('pscsx3241')->uninstall();
        }
        if (Module::isInstalled('pscsx32412')) {
            Module::getInstanceByName('pscsx32412')->uninstall();
        }
        if (Module::isInstalled('testconflict')) {
            Module::getInstanceByName('testconflict')->uninstall();
        }
        if (Module::isInstalled('testtrickyconflict')) {
            Module::getInstanceByName('testtrickyconflict')->uninstall();
        }
        if (Module::isInstalled('testpropertyconflict')) {
            Module::getInstanceByName('testpropertyconflict')->uninstall();
        }
        if (Module::isInstalled('testtypedpropertyoverride')) {
            Module::getInstanceByName('testtypedpropertyoverride')->uninstall();
        }
        if (Module::isInstalled('testmultilinepropertyoverride')) {
            Module::getInstanceByName('testmultilinepropertyoverride')->uninstall();
        }
        if (Module::isInstalled('testmoduleclassoverride')) {
            Module::getInstanceByName('testmoduleclassoverride')->uninstall();
        }
        if (Module::isInstalled('testmodifiersoverride')) {
            Module::getInstanceByName('testmodifiersoverride')->uninstall();
        }

        // Remove modules
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx3241')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx3241');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/pscsx32412')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/pscsx32412');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testconflict');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testtrickyconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testtrickyconflict');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testpropertyconflict')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testpropertyconflict');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testtypedpropertyoverride')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testtypedpropertyoverride');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testmultilinepropertyoverride')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testmultilinepropertyoverride');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testmodifiersoverride')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testmodifiersoverride');
        }
        if (is_dir(_PS_MODULE_DIR_ . '/testmoduleclassoverride')) {
            Tools::deleteDirectory(_PS_MODULE_DIR_ . '/testmoduleclassoverride');
        }

        // Remove overrides
        @unlink(_PS_ROOT_DIR_ . '/override/controllers/admin/DummyAdminController.php');
        @unlink(_PS_ROOT_DIR_ . '/override/classes/Cart.php');
        @unlink(_PS_ROOT_DIR_ . '/override/modules/pscsx3241/pscsx3241.php');
        @unlink(_PS_ROOT_DIR_ . '/override/modules/pscsx3241/index.php');
        @rmdir(_PS_ROOT_DIR_ . '/override/modules/pscsx3241');
        @unlink(_PS_ROOT_DIR_ . '/override/modules/index.php');
        @rmdir(_PS_ROOT_DIR_ . '/override/modules');

        // Reset modules folder
        (new ResourceResetter())->resetTestModules();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Context::getContext()->employee = new Employee(1);

        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();

        $this->moduleNames = [
            'pscsx32412',
            'pscsx3241',
            'testtypedpropertyoverride',
            'testmultilinepropertyoverride',
            'testmodifiersoverride',
        ];

        $this->conflictModuleNames = ['testbasicconflict', 'testtrickyconflict', 'testpropertyconflict'];
    }

    public function testInstall(): void
    {
        /*
         * Both modules install overrides in the same files.
         * This test only checks that modules are installed properly.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool) $this->moduleManager->install($name));
        }

        /**
         * This tests first checks that the overrides installed in the previous step
         * resulted in the expected merged files.
         */
        $resource_path = dirname(__DIR__, 4) . '/Resources/modules_tests/override/';

        $actual_override_cart = $this->cleanup(file_get_contents(_PS_ROOT_DIR_ . '/override/classes/Cart.php'));
        $expected_override_cart = $this->cleanup(file_get_contents($resource_path . 'classes/Cart.php'));

        $this->assertEquals(
            $expected_override_cart,
            $actual_override_cart,
            'Cart.php file different'
        );

        $actual_override_admin_product = $this->cleanup(file_get_contents(_PS_ROOT_DIR_ . '/override/controllers/admin/DummyAdminController.php'));
        $expected_override_admin_product = $this->cleanup(file_get_contents($resource_path . '/controllers/admin/DummyAdminController.php'));

        $this->assertEquals(
            $actual_override_admin_product,
            $expected_override_admin_product,
            'DummyAdminController.php file different'
        );

        // Then it checks that the overrides are removed once the modules are uninstalled.
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool) $this->moduleManager->uninstall($name));
        }

        $this->assertFileDoesNotExist(_PS_ROOT_DIR_ . '/override/classes/Cart.php');
        $this->assertFileDoesNotExist(_PS_ROOT_DIR_ . '/override/controllers/admin/DummyAdminController.php');
    }

    public function testOverrideConflictAtInstall(): void
    {
        $this->moduleManager->install($this->moduleNames[1]);

        /*
         * this will test that install fails when module has a conflicting override,
         * using test modules "testbasicconflict" and "testtrickyconflict", tricky conflict
         * adds several spaces in function definition (it must still be detected as a conflicting method)
         */
        foreach ($this->conflictModuleNames as $name) {
            $this->assertFalse($this->moduleManager->install($name), 'override conflict test on module ' . $name . ' failed');
        }
    }

    /**
     * Used to normalize the PHP source code for file comparison
     * and to strip dates that are inserted in comments when
     * overrides are installed.
     */
    public function testUninstallOnlyRemovesTheMembersOfTheModule(): void
    {
        $this->uninstallTestModules();
        $overridePath = _PS_ROOT_DIR_ . '/override/classes/Cart.php';
        $this->assertTrue((bool) $this->moduleManager->install('testmultilinepropertyoverride'));
        $this->assertTrue((bool) $this->moduleManager->install('testmodifiersoverride'));

        $this->assertTrue((bool) $this->moduleManager->uninstall('testmodifiersoverride'));
        $override = file_get_contents($overridePath);
        // Every member of the uninstalled module is gone, its markers included
        foreach ([
            'module: testmodifiersoverride',
            'TEST_FINAL_CONSTANT',
            'TEST_PRIVATE_CONSTANT',
            '$testNullableStaticProperty',
            '$testReadonlyProperty',
            '$testUnionProperty',
            '$testFqcnProperty',
            '$testReferenceStorage',
            'testFinalStaticMethod',
            'testStaticMethod',
            'testReferenceMethod',
        ] as $member) {
            $this->assertStringNotContainsString($member, $override);
        }
        // The members of the other module are untouched, properties and constants included
        foreach ([
            'module: testmultilinepropertyoverride',
            '$testMultilineProperty',
            "'key2' => 'value2'",
            'TEST_MULTILINE_CONSTANT',
            "'const_key2' => 'const_value2'",
        ] as $member) {
            $this->assertStringContainsString($member, $override);
        }

        $this->assertTrue((bool) $this->moduleManager->uninstall('testmultilinepropertyoverride'));
        $this->assertFileDoesNotExist($overridePath);
    }

    public function testUninstallRemovesOverridesWhenModuleSourcesAreGone(): void
    {
        $this->uninstallTestModules();
        $overridePath = _PS_ROOT_DIR_ . '/override/classes/Cart.php';
        $moduleOverrideDir = _PS_MODULE_DIR_ . '/testmodifiersoverride/override';
        $this->assertTrue((bool) $this->moduleManager->install('testmodifiersoverride'));
        $this->assertFileExists($overridePath);

        // The module has been upgraded to a version which does not ship this override anymore
        Tools::deleteDirectory($moduleOverrideDir);
        try {
            $this->assertTrue((bool) $this->moduleManager->uninstall('testmodifiersoverride'));
            $this->assertFileDoesNotExist($overridePath);
        } finally {
            Tools::recurseCopy(dirname(__DIR__, 4) . '/Resources/modules_tests/testmodifiersoverride/override', $moduleOverrideDir);
        }
    }

    public function testModuleClassOverrideIsInstalledAndRemoved(): void
    {
        $this->uninstallTestModules();
        if (Module::isInstalled('testmoduleclassoverride')) {
            $this->moduleManager->uninstall('testmoduleclassoverride');
        }
        $overridePath = _PS_ROOT_DIR_ . '/override/modules/pscsx3241/pscsx3241.php';

        // The class of a module can only be overridden once this module is installed
        $this->assertTrue((bool) $this->moduleManager->install('pscsx3241'));
        $this->assertTrue((bool) $this->moduleManager->install('testmoduleclassoverride'));
        $this->assertFileExists($overridePath);
        // The override of a module class is copied as it is, without marker: its removal relies on the
        // override directory of the module
        $this->assertStringNotContainsString('module: testmoduleclassoverride', file_get_contents($overridePath));

        $this->assertTrue((bool) $this->moduleManager->uninstall('testmoduleclassoverride'));
        $this->assertFileDoesNotExist($overridePath);
        $this->assertTrue((bool) $this->moduleManager->uninstall('pscsx3241'));
    }

    /**
     * Previous tests may leave some modules installed
     */
    private function uninstallTestModules(): void
    {
        foreach ($this->moduleNames as $name) {
            if (Module::isInstalled($name)) {
                $this->moduleManager->uninstall($name);
            }
        }
    }

    private function cleanup(string $str): string
    {
        $withoutDate = preg_replace('#\* date: .*?\n#m', '', $str);

        return preg_replace('#\n?^(?:\s*)$#m', '', $withoutDate);
    }
}
