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

namespace Tests\Unit\Adapter\Module\Repository;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleRepository = new ModuleRepository();
    }

    public function testNativeModulesMandatoryModules(): void
    {
        $modules = $this->moduleRepository->getNativeModules();
        foreach (ModuleRepository::ADDITIONAL_ALLOWED_MODULES as $mandatoryModule) {
            self::assertContains($mandatoryModule, $modules);
        }
    }

    /**
     * @dataProvider dataProviderNativeModules
     *
     * @param string $moduleName
     * @param bool $isNative
     */
    public function testNativeModulesCheckModules(string $moduleName, bool $isNative): void
    {
        if ($isNative) {
            self::assertContains($moduleName, $this->moduleRepository->getNativeModules());
        } else {
            self::assertNotContains($moduleName, $this->moduleRepository->getNativeModules());
        }
    }

    public function dataProviderNativeModules(): iterable
    {
        // Native modules
        yield ['blockwishlist', true];
        yield ['contactform', true];
        yield ['dashactivity', true];
        yield ['dashgoals', true];
        yield ['dashproducts', true];
        yield ['dashtrends', true];
        yield ['graphnvd3', true];
        yield ['gridhtml', true];
        yield ['gsitemap', true];
        yield ['pagesnotfound', true];
        yield ['ps_banner', true];
        yield ['ps_bestsellers', true];
        yield ['ps_brandlist', true];
        yield ['ps_cashondelivery', true];
        yield ['ps_categoryproducts', true];
        yield ['ps_categorytree', true];
        yield ['ps_checkpayment', true];
        yield ['ps_contactinfo', true];
        yield ['ps_crossselling', true];
        yield ['ps_currencyselector', true];
        yield ['ps_customeraccountlinks', true];
        yield ['ps_customersignin', true];
        yield ['ps_customtext', true];
        yield ['ps_dataprivacy', true];
        yield ['ps_emailsubscription', true];
        yield ['ps_facetedsearch', true];
        yield ['ps_faviconnotificationbo', true];
        yield ['ps_featuredproducts', true];
        yield ['ps_imageslider', true];
        yield ['ps_languageselector', true];
        yield ['ps_linklist', true];
        yield ['ps_mainmenu', true];
        yield ['ps_newproducts', true];
        yield ['ps_searchbar', true];
        yield ['ps_sharebuttons', true];
        yield ['ps_shoppingcart', true];
        yield ['ps_socialfollow', true];
        yield ['ps_specials', true];
        yield ['ps_supplierlist', true];
        yield ['ps_themecusto', true];
        yield ['ps_viewedproduct', true];
        yield ['ps_wirepayment', true];
        yield ['statsbestcategories', true];
        yield ['statsbestcustomers', true];
        yield ['statsbestmanufacturers', true];
        yield ['statsbestproducts', true];
        yield ['statsbestsuppliers', true];
        yield ['statsbestvouchers', true];
        yield ['statscarrier', true];
        yield ['statscatalog', true];
        yield ['statscheckup', true];
        yield ['statsdata', true];
        yield ['statsforecast', true];
        yield ['statsnewsletter', true];
        yield ['statspersonalinfos', true];
        yield ['statsproduct', true];
        yield ['statsregistrations', true];
        yield ['statssales', true];
        yield ['statssearch', true];
        yield ['statsstock', true];
        // Non native modules
        yield ['ps_checkout', false];
        yield ['azerty', false];
        yield ['', false];
    }
}
