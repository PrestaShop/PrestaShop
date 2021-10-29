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

declare(strict_types=1);

namespace Tests\Integration\Core\Addon\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Translator;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    protected function setUp(): void
    {
        /** We need a mock in order to change the module folder */
        $moduleDataProvider = $this->getMockBuilder(ModuleDataProvider::class)->disableOriginalConstructor()->getMock();
        $moduleDataProvider->method('findByName')->willReturn([
            'installed' => 0,
            'active' => true,
        ]);
        // required to have 'productType' field of module set up
        $moduleDataProvider->method('isModuleMainClassValid')->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);

        $addonsDataProvider = $this->getMockBuilder(AddonsDataProvider::class)->disableOriginalConstructor()->getMock();

        $categoriesProvider = $this->getMockBuilder(CategoriesProvider::class)->disableOriginalConstructor()->getMock();

        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
        $translator->method('trans')->willReturnArgument(0);

        $adminModuleDataProvider = $this->getMockBuilder(AdminModuleDataProvider::class)
            ->setConstructorArgs([$translator, $logger, $addonsDataProvider, $categoriesProvider, $moduleDataProvider])
            ->setMethods(['getCatalogModulesNames'])
            ->getMock();

        $adminModuleDataProvider->method('getCatalogModulesNames')->willReturn([]);

        $this->moduleRepository = $this->getMockBuilder(ModuleRepository::class)
            ->setConstructorArgs([
                $adminModuleDataProvider,
                $moduleDataProvider,
                new ModuleDataUpdater(
                    $addonsDataProvider,
                    new AdminModuleDataProvider(
                        $translator,
                        $logger,
                        $addonsDataProvider,
                        $categoriesProvider,
                        $moduleDataProvider
                    )
                ),
                $logger,
                $translator,
                dirname(__DIR__, 4) . '/Resources/modules/',
            ])
            ->setMethods(['readCacheFile', 'generateCacheFile'])
            ->getMock();

        /* Mock function to disable the cache */
        $this->moduleRepository->method('readCacheFile')->willReturn([]);
        $this->moduleRepository->method('generateCacheFile')->will($this->returnArgument(0));
    }

    public function testGetAtLeastOneModuleFromUniverse(): void
    {
        $this->assertGreaterThan(0, count($this->moduleRepository->getList()));
    }

    public function testGetOnlyInstalledModules(): void
    {
        $all_modules = $this->moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters
            ->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED);

        $installed_modules = $this->moduleRepository->getFilteredList($filters);

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

    public function testGetOnlyNOTInstalledModules(): void
    {
        $all_modules = $this->moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~AddonListFilterStatus::INSTALLED);

        $not_installed_modules = $this->moduleRepository->getFilteredList($filters);

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

    public function testGetOnlyEnabledModules(): void
    {
        $all_modules = $this->moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::ENABLED);

        $installed_and_active_modules = $this->moduleRepository->getFilteredList($filters);

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

    public function testGetNotEnabledModules(): void
    {
        $all_modules = $this->moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(~AddonListFilterStatus::ENABLED);

        $not_active_modules = $this->moduleRepository->getFilteredList($filters);

        foreach ($not_active_modules as $module) {
            $this->assertTrue($module->database->get('active') == 0);
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->attributes->get('productType') == 'module' && $module->database->get('installed') == 1 && $module->database->get('active') == 0) {
                $this->assertArrayHasKey($name, $not_active_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetInstalledButDisabledModules(): void
    {
        $all_modules = $this->moduleRepository->getList();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->setStatus(AddonListFilterStatus::INSTALLED & ~AddonListFilterStatus::ENABLED);

        $installed_but_not_installed_modules = $this->moduleRepository->getFilteredList($filters);

        foreach ($installed_but_not_installed_modules as $module) {
            $this->assertTrue($module->database->get('installed') == 1, $module->attributes->get('name') . ' marked as not installed ><');
            $this->assertTrue($module->database->get('active') == 0, $module->attributes->get('name') . ' marked as enabled ><');
        }

        foreach ($all_modules as $name => $module) {
            // Each installed module must be found in the installed modules list
            if ($module->database->get('installed') == 1 && $module->database->get('active') == 0) {
                $this->assertArrayHasKey($name, $installed_but_not_installed_modules, sprintf('Module %s not found in the filtered list !', $name));
            }
        }
    }

    public function testGetAddonsFromMarketplaceOnly(): void
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($this->moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->has('origin'), $module->attributes->get('name') . ' has not an origin attribute');
        }
    }

    public function testGetAddonsNotOnMarketplace1(): void
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(AddonListFilterOrigin::DISK);

        // Each module must have its origin attribute
        foreach ($this->moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name') . ' has an origin attribute, but should not');
        }
    }

    public function testGetAddonsNotOnMarketplace2(): void
    {
        $filters = new AddonListFilter();
        $filters->setOrigin(~AddonListFilterOrigin::ADDONS_ALL);

        // Each module must have its origin attribute
        foreach ($this->moduleRepository->getFilteredList($filters) as $module) {
            $this->assertFalse($module->attributes->has('origin'), $module->attributes->get('name') . ' has an origin attribute, but should not !');
        }
    }

    /**
     * @todo how to get fake modules returned ?
     */
    public function testGetOnlyModules(): void
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE);

        foreach ($this->moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'module', $module->attributes->get('name') . ' has a product type "' . $module->attributes->get('productType') . '"');
        }
    }

    public function testGetOnlyServices(): void
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::SERVICE);

        foreach ($this->moduleRepository->getFilteredList($filters) as $module) {
            $this->assertTrue($module->attributes->get('productType') == 'service');
        }
    }

    public function testShouldNotBeAbleToReturnTheme(): void
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::THEME);

        $this->assertCount(0, $this->moduleRepository->getFilteredList($filters));
    }
}
