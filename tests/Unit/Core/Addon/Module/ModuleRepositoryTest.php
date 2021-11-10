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

namespace Tests\Unit\Core\Addon\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @dataProvider dataProviderGetFilteredListWithConfigDisabledNonNativeModules
     *
     * @param bool $isDisabledNonNativeModules
     * @param array $expectedList
     */
    public function testGetFilteredListWithConfigDisabledNonNativeModules(bool $isDisabledNonNativeModules, array $expectedList): void
    {
        $mockModuleDataProvider = $this->createMock(ModuleDataProvider::class);
        $mockModuleDataProvider->method('findByName')->willReturn([]);

        $mockConfiguration = $this->createMock(ConfigurationInterface::class);
        $mockConfiguration->method('get')->willReturnCallback(
            function (string $config) use ($isDisabledNonNativeModules) {
                if ($config === 'PS_DISABLE_NON_NATIVE_MODULE') {
                    return $isDisabledNonNativeModules;
                }

                return false;
            }
        );

        $mockAdminModuleDataProvider = $this->createMock(AdminModuleDataProvider::class);
        $mockAdminModuleDataProvider->method('getCatalogModules')->willReturn([]);

        $moduleRepository = new ModuleRepository(
            $mockAdminModuleDataProvider,
            $mockModuleDataProvider,
            $this->createMock(ModuleDataUpdater::class),
            $this->createMock(LoggerInterface::class),
            $this->createMock(TranslatorInterface::class),
            _PS_MODULE_DIR_,
            $mockConfiguration
        );
        $actualList = $moduleRepository->getFilteredList(
            new AddonListFilter(),
            true
        );
        $actualList = array_keys($actualList);
        sort($actualList);

        self::assertEquals($expectedList, $actualList);
    }

    public function dataProviderGetFilteredListWithConfigDisabledNonNativeModules(): iterable
    {
        yield [
            true,
            [
                'ps_banner',
                'ps_emailsubscription',
                'ps_featuredproducts',
            ],
        ];
        yield [
            false,
            [
                'bankwire',
                'cronjobs',
                'demo',
                'dummy_payment',
                'ganalytics',
                'ps_banner',
                'ps_emailsubscription',
                'ps_featuredproducts',
                'translationtest',
                'xlftranslatedmodule',
            ],
        ];
    }
}
