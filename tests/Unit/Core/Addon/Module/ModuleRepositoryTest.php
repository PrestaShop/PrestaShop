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

use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

/**
 * Description of ModuleRepositoryTest
 *
 * @author thomas
 */
class ModuleRepositoryTest extends \PHPUnit_Framework_TestCase
{

    private $moduleManagerFactory;

    private $http_host_not_found = false;

    public function setUp()
    {
        parent::setUp();
        if (!defined('__PS_BASE_URI__')) {
            define('__PS_BASE_URI__', "http://www.example.com/shop");
        }
        if (!defined('_THEME_DIR_')) {
            define('_THEME_NAME_', 'classic');
            define('_PS_THEME_DIR_', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/');
            define('_THEMES_DIR_', __PS_BASE_URI__.'themes/');
            define('_THEME_DIR_', _THEMES_DIR_._THEME_NAME_.'/');
        }

        if (! isset($_SERVER['HTTP_HOST'])) {
            $this->http_host_not_found = true;
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        $context = \Context::getContext();
        $context->language = new \stdClass();
        $context->language->id = 42;
        $context->language->iso_code = 'fr';
        $context->shop->id = 1;

        $this->moduleManagerFactory = new ModuleManagerBuilder();
    }

    public function test_module_repository_is_built()
    {
        $this->assertNotNull($this->moduleManagerFactory->buildRepository());
    }

    public function test_get_at_least_one_module_from_universe()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $this->assertGreaterThan(0, count($moduleRepository->getList()));
    }

    public function test_get_only_installed_modules()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->has('origin'), $module->attributes->get('name').' has not an origin attribute');
        }
    }

    public function test_get_addons_not_on_marketplace_1()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterOrigin::DISK);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not');
        }
    }

    public function test_get_addons_not_on_marketplace_2()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $filters = new AddonListFilter();
        $filters->setType(~AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not !');
        }
    }

    public function test_get_only_modules()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'module', $module->attributes->get('name').' has a product type "'. $module->attributes->get('productType') .'"');
        }
    }

    public function test_get_only_services()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::SERVICE);

        foreach ($moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'service');
        }
    }

    public function test_should_not_be_able_to_return_theme()
    {
        $moduleRepository = $this->moduleManagerFactory->buildRepository();
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
    }
}
