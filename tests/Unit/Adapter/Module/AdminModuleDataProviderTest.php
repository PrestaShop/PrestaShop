<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\tests\Unit\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Phake;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AdminModuleDataProviderTest extends UnitTestCase
{
    private $legacyContext;
    private $http_host_not_found = false;

    public function setUp()
    {
        parent::setUp();

        if (!defined('__PS_BASE_URI__')) {
            define('__PS_BASE_URI__', "http://www.example.com/shop");
        }

        $this->context->language = new \stdClass();
        $this->context->language->id = 42;
        $this->context->language->iso_code = 'fr';
        $this->legacyContext = Phake::partialMock('PrestaShop\\PrestaShop\\Adapter\\LegacyContext');
        Phake::when($this->legacyContext)->getAdminBaseUrl()->thenReturn('admin_fake_base');

        if (! isset($_SERVER['HTTP_HOST'])) {
            $this->http_host_not_found = true;
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        $this->setupSfKernel();

        // We try to load the Addons catalog. If it fails, we skip the tests of this file.
        try {
            $dataProvider = new AdminModuleDataProvider($this->sfKernel);
            $dataProvider->getCatalogModules();
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Data from PrestaShop Addons is invalid, and cannot fallback on cache') {
                $this->markTestSkipped('The Addons catalog is not available, test skipped x_x');
            }
        }
    }

    public function test_modules_in_catalog()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        $modules = $dataProvider->getCatalogModules();
        $categories = $dataProvider->getCatalogCategories();

        $this->assertGreaterThan(0, count($modules));
        $this->assertGreaterThan(0, count($categories));
    }

    public function test_modules_in_categories_can_be_found_in_module_list()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        $modules = $dataProvider->getCatalogModules();
        $categories = $dataProvider->getCatalogCategories();

        // For each category ...
        foreach ((array)$categories->categories->subMenu as $category) {
            // For each module ID linked to this category
            foreach ($category->modulesRef as $moduleId) {

                // We look for the module in the modules list
                foreach ($modules as $module) {
                    if ($module->id == $moduleId) {
                        // The IDs given are related to a module in the list
                        //$this->assertContains($moduleId, $moduleIds);

                        // We also check that the module has also a ref to the current category we test
                        $this->assertTrue(in_array($category->refMenu, $module->refs));

                        continue 2;
                    }
                }
                $this->fail('Module with the ID "'. $moduleId .'" not found.');
            }
        }
    }

    public function test_no_results()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        $filters = ['search' => 'doge'];
        $modules = $dataProvider->getCatalogModules($filters);

        $this->assertCount(0, $modules);
    }

    public function test_unknown_filter_criteria()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        // An unexpected critera should have no effect on the module list
        $filters = ['random_filter' => 'doge'];
        $modules = $dataProvider->getCatalogModules($filters);

        $all_modules = $dataProvider->getCatalogModules();

        $this->assertEquals($all_modules, $modules);
    }

    public function test_specific_module_search()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        // An unexpected critera should have no effect on the module list
        $filters = ['search' => 'ganalytics'];
        $modules = $dataProvider->getCatalogModules($filters);

        $this->assertCount(1, $modules);
    }

    public function test_specific_module_search_2_results()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        // An unexpected critera should have no effect on the module list
        $filters = ['search' => 'ganalytics gapi'];
        $modules = $dataProvider->getCatalogModules($filters);

        $this->assertCount(2, $modules);
    }

    public function test_only_one_call_to_addons_and_same_result()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);
        $mock = $this->getMock('PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider', ['convertJsonForNewCatalog'], ['kernel' => $this->sfKernel]);
        $mock->expects($this->once())->method('convertJsonForNewCatalog')->will($this->returnValue($dataProvider->getCatalogModules()));

        $mock->clearCatalogCache();

        $modules = $mock->getCatalogModules();
        $modules2 = $mock->getCatalogModules();

        $this->assertEquals($modules2, $modules);
    }

    public function test_product_type_correct()
    {
        $dataProvider = new AdminModuleDataProvider($this->sfKernel);

        $modules = $dataProvider->getCatalogModules();
        $possible_values = ['module', 'service', 'theme'];
        foreach ($modules as $module) {
            $this->assertTrue(in_array($module->productType, $possible_values));
        }
    }

    public function teardown()
    {
        parent::teardown();

        if ($this->http_host_not_found) {
            unset($_SERVER['HTTP_HOST']);
        }
    }
}
