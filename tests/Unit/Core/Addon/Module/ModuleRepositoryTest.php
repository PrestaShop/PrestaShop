<?php
/*
 * 2007-2016 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Core\Addon\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Phake;

/**
 * Description of ModuleRepositoryTest
 *
 * @author thomas
 */
class ModuleRepositoryTest extends \PHPUnit_Framework_TestCase
{

    private $moduleRepositoryStub;
    private $moduleDataProviderStub;

    private $http_host_not_found = false;

    public function setUp()
    {
        if (!defined('__PS_BASE_URI__')) {
            define('__PS_BASE_URI__', "http://www.example.com/shop");
        }

        $this->moduleDataProviderStub = $this->getMock(
            'PrestaShop\\PrestaShop\\Adapter\\Module\\ModuleDataProvider',
            ['getModulesDir']
        );
        $this->moduleDataProviderStub->expects($this->any())
             ->method('getModulesDir')
             ->will($this->returnValue(_PS_ROOT_DIR_.'/tests/resources/modules/'));

        

        $this->moduleRepositoryStub = $this->getMock(
            'PrestaShop\\PrestaShop\\Core\\Addon\\Module\\ModuleRepository',
            ['getModulesDir', 'readCacheFile'],
            [
                new AdminModuleDataProvider(),
                $this->moduleDataProviderStub,
                new ModuleDataUpdater()
            ]
        );
        $this->moduleRepositoryStub->expects($this->any())
             ->method('getModulesDir')
             ->will($this->returnValue(_PS_ROOT_DIR_.'/tests/resources/modules/'));
        $this->moduleRepositoryStub->expects($this->any())
             ->method('readCacheFile')
             ->will($this->returnValue([]));
    }

    public function test_module_repository_is_built()
    {
        $moduleManagerBuilder = new ModuleManagerBuilder();
        $this->assertNotNull($moduleManagerBuilder->buildRepository());
    }

    public function test_get_at_least_one_module_from_universe()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $this->assertGreaterThan(0, count($moduleRepository->getList()));
    }

    public function test_get_only_installed_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $all_modules = $moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED);

        // Each module MUST have its database installed attribute as true
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->database->get('installed') == 1);
        }
    }

    public function test_get_only_NOT_installed_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $all_modules = $moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~ AddonListFilterStatus::INSTALLED);

        // Each module MUST have its database installed attribute as true
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->database->get('installed') == 0);
        }
    }

    public function test_get_only_enabled_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $all_modules = $moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::ENABLED);

        // Each module MUST have its database installed and enabled attributes as true
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->database->get('installed') == 1);
            $this->assertTrue($module->database->get('active') == 1);
        }
    }

    public function test_get_not_enabled_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $all_modules = $moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~ AddonListFilterStatus::ENABLED);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->database->get('active') == 0);
        }
    }

    public function test_get_installed_but_disabled_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $all_modules = $moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED &~ AddonListFilterStatus::ENABLED);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->database->get('installed') == 1, $module->attributes->get('name') .' marked as not installed ><');
            $this->assertTrue($module->database->get('active') == 0, $module->attributes->get('name') .' marked as enabled ><');
        }
    }

    public function test_get_addons_from_marketplace_only()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->has('origin'), $module->attributes->get('name').' has not an origin attribute');
        }
    }

    public function test_get_addons_not_on_marketplace_1()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::DISK);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not');
        }
    }

    public function test_get_addons_not_on_marketplace_2()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setOrigin(~AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not !');
        }
    }

    public function test_get_only_modules()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'module', $module->attributes->get('name').' has a product type "'. $module->attributes->get('productType') .'"');
        }
    }

    public function test_get_only_services()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::SERVICE);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'service');
        }
    }

    public function test_should_not_be_able_to_return_theme()
    {
        $moduleRepository = $this->moduleRepositoryStub;
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::THEME);

        $this->assertEquals(0, count($moduleRepository->getFilteredList($filters)));
    }

    public function teardown()
    {
        parent::teardown();

        if ($this->http_host_not_found) {
            unset($_SERVER['HTTP_HOST']);
        }

        $moduleManagerBuilder = new ModuleManagerBuilder();
        $moduleRepository = $moduleManagerBuilder->buildRepository();
        $moduleRepository->clearCache();
    }
}
