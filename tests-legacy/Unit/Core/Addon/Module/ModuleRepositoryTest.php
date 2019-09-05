<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Core\Addon\Module;

use LegacyTests\TestCase\FakeLogger;
use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;

/**
 * @runInSeparateProcess
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 *
 * Note theses annotations are required because we mock constants.
 */
class ModuleRepositoryTest extends UnitTestCase
{
    private $moduleRepositoryStub;
    private $moduleDataProviderStub;
    private $categoriesProviderS;

    private $http_host_not_found = false;

    protected function setUp()
    {
        if (!defined('__PS_BASE_URI__')) {
            define('__PS_BASE_URI__', 'http://www.example.com/shop');
        }

        if (!defined('_PS_THEME_DIR_')) {
            define('_PS_THEME_DIR_', _PS_ROOT_DIR_.'/themes/classic/');
        }

        if (!isset($_SERVER['HTTP_HOST'])) {
            $this->http_host_not_found = true;
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        /*
         * We need a mock in order to change the module folder
         */
        $this->moduleDataProviderStub = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->moduleDataProviderStub
            ->method('findByName')
            ->willReturn(array(
                'installed' => 0,
                'active' => true,
            ));
        // required to have 'productType' field of module set up
        $this->moduleDataProviderStub
            ->method('isModuleMainClassValid')
            ->willReturn(true);

        $this->setupSfKernel();
        $this->logger = $this->sfKernel->getContainer()->get('logger');

        $this->apiClientS = $this->getMockBuilder('PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->addonsDataProviderS = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoriesProviderS = $this->getMockBuilder('PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider')
            ->disableOriginalConstructor()
            ->getmock();

        $this->translatorStub = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->translatorStub
            ->method('trans')
            ->will($this->returnArgument(0));

        $this->adminModuleDataProviderStub = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider')
            ->setConstructorArgs(array($this->translatorStub, $this->logger, $this->addonsDataProviderS, $this->categoriesProviderS, $this->moduleDataProviderStub))
            ->setMethods(array('getCatalogModulesNames'))
            ->getMock();

        $this->adminModuleDataProviderStub
            ->method('getCatalogModulesNames')
            ->willReturn(array());

        $this->moduleRepositoryStub = $this->getMockBuilder('PrestaShop\\PrestaShop\\Core\\Addon\\Module\\ModuleRepository')
            ->setConstructorArgs(array(
                $this->adminModuleDataProviderStub,
                $this->moduleDataProviderStub,
                new ModuleDataUpdater(
                    $this->addonsDataProviderS,
                    new AdminModuleDataProvider(
                        $this->translatorStub,
                        $this->logger,
                        $this->addonsDataProviderS,
                        $this->categoriesProviderS,
                        $this->moduleDataProviderStub
                    )
                ),
                new FakeLogger(),
                $this->translatorStub,
                __DIR__.'/../../../../resources/modules/',
            ))
            ->setMethods(array('readCacheFile', 'generateCacheFile'))
            ->getMock();

        /*
         * Mock function 'readCacheFile()' to disable the cache
         */
        $this->moduleRepositoryStub
            ->method('readCacheFile')
            ->willReturn(array());

        /*
         * Mock function 'readCacheFile()' to disable the cache
         */
        $this->moduleRepositoryStub
            ->method('generateCacheFile')
            ->will($this->returnArgument(0));

        /*
         * End of mocking for modules folder
         */
    }

    public function testGetAtLeastOneModuleFromUniverse()
    {
        $this->assertGreaterThan(0, count($this->moduleRepositoryStub->getList()));
    }

    public function testGetOnlyInstalledModules()
    {
        $all_modules = $this->moduleRepositoryStub->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED);

        $installed_modules = $this->moduleRepositoryStub->getFilteredList($filters);

        // Each module MUST have its database installed attribute as true
        foreach ($installed_modules as $module) {
            $this->assertTrue($module->database->get('installed') == 1);
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->database->get('installed') == 1) {
                $this->assertArrayHasKey($name, $installed_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetOnlyNOTInstalledModules()
    {
        $all_modules = $this->moduleRepositoryStub->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~AddonListFilterStatus::INSTALLED);

        $not_installed_modules = $this->moduleRepositoryStub->getFilteredList($filters);

        // Each module MUST have its database installed attribute as true
        foreach ($not_installed_modules as $module) {
            $this->assertTrue($module->database->get('installed') == 0);
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->attributes->get('productType') == 'module' && $module->database->get('installed') == 0) {
                $this->assertArrayHasKey($name, $not_installed_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetOnlyEnabledModules()
    {
        $all_modules = $this->moduleRepositoryStub->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::ENABLED);

        $installed_and_active_modules = $this->moduleRepositoryStub->getFilteredList($filters);

        // Each module MUST have its database installed and enabled attributes as true
        foreach ($installed_and_active_modules as $module) {
            $this->assertTrue($module->database->get('installed') == 1);
            $this->assertTrue($module->database->get('active') == 1);
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->database->get('installed') == 1
                && $module->database->get('active') == 1) {
                $this->assertArrayHasKey($name, $installed_and_active_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetNotEnabledModules()
    {
        $all_modules = $this->moduleRepositoryStub->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~AddonListFilterStatus::ENABLED);

        $not_active_modules = $this->moduleRepositoryStub->getFilteredList($filters);

        foreach ($not_active_modules as $module) {
            $this->assertTrue($module->database->get('active') == 0);
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->attributes->get('productType') == 'module' && $module->database->get('installed') == 1  && $module->database->get('active') == 0) {
                $this->assertArrayHasKey($name, $not_active_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetInstalledButDisabledModules()
    {
        $all_modules = $this->moduleRepositoryStub->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED & ~AddonListFilterStatus::ENABLED);

        $installed_but_not_installed_modules = $this->moduleRepositoryStub->getFilteredList($filters);

        foreach ($installed_but_not_installed_modules as $module) {
            $this->assertTrue($module->database->get('installed') == 1, $module->attributes->get('name').' marked as not installed ><');
            $this->assertTrue($module->database->get('active') == 0, $module->attributes->get('name').' marked as enabled ><');
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->database->get('installed') == 1 && $module->database->get('active') == 0) {
                $this->assertArrayHasKey($name, $installed_but_not_installed_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetAddonsFromMarketplaceOnly()
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($this->moduleRepositoryStub->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->has('origin'), $module->attributes->get('name').' has not an origin attribute');
        }
    }

    public function testGetAddonsNotOnMarketplace1()
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::DISK);

        // Each module must have its origin attribute
        foreach ($this->moduleRepositoryStub->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not');
        }
    }

    public function testGetAddonsNotOnMarketplace2()
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(~AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($this->moduleRepositoryStub->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name').' has an origin attribute, but should not !');
        }
    }

    /**
     * @todo how to get fake modules returned ?
     */
    public function testGetOnlyModules()
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE);

        foreach ($this->moduleRepositoryStub->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'module', $module->attributes->get('name').' has a product type "'.$module->attributes->get('productType').'"');
        }
    }

    public function testGetOnlyServices()
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::SERVICE);

        foreach ($this->moduleRepositoryStub->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'service');
        }
    }

    public function testShouldNotBeAbleToReturnTheme()
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::THEME);

        $this->assertCount(0, $this->moduleRepositoryStub->getFilteredList($filters));
    }

    protected function teardown()
    {
        parent::teardown();

        if ($this->http_host_not_found) {
            unset($_SERVER['HTTP_HOST']);
        }
    }
}
