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

namespace Tests\Unit\Adapter\Module;

use Doctrine\Common\Cache\CacheProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Translation\TranslatorInterface;

class AdminModuleDataProviderTest extends TestCase
{
    /**
     * @var string
     */
    private const NOTICE = '[AdminModuleDataProvider] ';

    /**
     * @var AddonsInterface
     */
    private $addonsDataProvider;

    /**
     * @var AdminModuleDataProvider
     */
    private $adminModuleDataProvider;

    /**
     * @var CacheProvider
     */
    private $cacheProvider;

    /**
     * @var CategoriesProvider
     */
    private $categoriesProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ModuleDataProvider
     */
    private $moduleProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    protected function setUp(): void
    {
        parent::setUp();

        /* The module catalog will contains only 5 modules for theses tests */
        $fakeModules = [
            $this->fakeModule(
                1,
                'pm_advancedpack',
                'Advanced Pack 5 - Create bundles of products',
                'Cross-selling & Product Bundles',
                'Allows the sale batch using any stocks actually available products composing your packs, and offers the opportunity to apply business operations'
            ),
            $this->fakeModule(
                2,
                'cmcicpaiement',
                'CM-CIC / Monetico Payment in one instalment',
                'Payment by Card or Wallet',
                'Accept bank card payments in your online shop with the CM-CIC / Monetico p@yment&nbsp;module!  This very popular means of secure payment reassures your customers when they make their purchases in your'
            ),
            $this->fakeModule(
                3,
                'bitcoinpayment',
                'Coinbase Payment (Bitcoin)',
                'Other Payment Methods',
                'Use the Coinbase payment module to give your customers the possibility of paying for their purchases in your store with Bitcoin!  This module uses the API from Coinbase, a globally recognized Bitcoin'
            ),
            $this->fakeModule(
                4,
                'fake_module',
                'Fake module 1',
                'PHPUnit Fakes',
                ''
            ),
            $this->fakeModule(
                5,
                'fake_module_2',
                'Fake module 2',
                'PHPUnit Fakes',
                ''
            ),
        ];

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->addonsDataProvider = $this->createMock(AddonsInterface::class);
        $this->categoriesProvider = $this->createMock(CategoriesProvider::class);
        $this->moduleProvider = $this->createMock(ModuleDataProvider::class);
        $this->cacheProvider = $this->createMock(CacheProvider::class);
        $this->cacheProvider->method('contains')->withAnyParameters('_addons_modules')->willReturn(true);
        $this->cacheProvider->method('fetch')->withAnyParameters('_addons_modules')->willReturn($fakeModules);

        $this->adminModuleDataProvider = new AdminModuleDataProvider(
            $this->translator,
            $this->logger,
            $this->addonsDataProvider,
            $this->categoriesProvider,
            $this->moduleProvider,
            $this->cacheProvider
        );
    }

    public function testGetListOfModulesOk(): void
    {
        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertGreaterThan(0, count($modules), sprintf('%s expected a list of modules, received none.', self::NOTICE));
    }

    public function testSearchCanResultNoResultsOk(): void
    {
        $filters = ['search' => 'doge'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(0, $modules, sprintf('%s expected 0 modules, received %s.', self::NOTICE, count($modules)));
    }

    public function testSearchWithUnknownFilterCriteriaReturnAllOk(): void
    {
        $filters = ['random_filter' => 'doge'];
        $modulesWithFilter = $this->adminModuleDataProvider->getCatalogModules($filters);

        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertSame($modulesWithFilter, $modules, sprintf('%s expected undefined filter have no effect on search.', self::NOTICE));
    }

    public function testSearchForASpecificModuleOk(): void
    {
        $filters = ['search' => 'advancedpack'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(1, $modules);
    }

    public function testSearchForASpecificModuleHaveMultipleResultsOk(): void
    {
        $filters = ['search' => 'payment advanced'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(3, $modules);
    }

    public function testCallToAddonsShouldReturnSameResultOk(): void
    {
        $mock = $this->getMockBuilder(AdminModuleDataProvider::class)
            ->setConstructorArgs([
                'translator' => $this->translator,
                'logger' => $this->logger,
                'addonsDataProvider' => $this->addonsDataProvider,
                'categoriesProvider' => $this->categoriesProvider,
                'modulesProvider' => $this->moduleProvider,
                'cacheProvider' => $this->cacheProvider,
            ])
            ->getMock();

        $mock->clearCatalogCache();

        $modules = $mock->getCatalogModules();
        $modules2 = $mock->getCatalogModules();

        $this->assertEquals($modules2, $modules);
    }

    private function fakeModule(int $id, string $name, string $displayName, string $categoryName, string $description): stdClass
    {
        $fakeModule = new stdClass();
        $fakeModule->id = $id;
        $fakeModule->name = $name;
        $fakeModule->displayName = $displayName;
        $fakeModule->categoryName = $categoryName;
        $fakeModule->description = $description;

        return $fakeModule;
    }
}
