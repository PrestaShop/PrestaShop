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

namespace Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ZoneByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CarrierFormDataProvider;

class CarrierFormDataProviderTest extends TestCase
{
    public function testGetData(): void
    {
        // Create a mock for CommandBusInterface
        $queryBus = $this->createMock(CommandBusInterface::class);
        $invokedCount = $this->exactly(2);
        $queryBus->expects($invokedCount)
            ->method('handle')
            ->willReturnCallback(function ($query) use ($invokedCount) {
                if ($invokedCount->numberOfInvocations() === 1) {
                    $this->assertInstanceOf(GetCarrierForEditing::class, $query);

                    return new EditableCarrier(
                        42,
                        'Carrier name',
                        5,
                        'http://track.to',
                        1,
                        true,
                        [
                            1 => 'English delay',
                            2 => 'French delay',
                        ],
                        1234,
                        1123,
                        3421,
                        1657,
                        [1, 2, 3],
                        false,
                        true,
                        1,
                        1,
                        OutOfRangeBehavior::USE_HIGHEST_RANGE,
                        [1, 3],
                        [1, 2],
                        '/img/c/45.jkg',
                    );
                }

                if ($invokedCount->numberOfInvocations() === 2) {
                    $this->assertInstanceOf(GetCarrierRanges::class, $query);

                    return new CarrierRangesCollection([
                        ['id_zone' => 1, 'range_from' => 0, 'range_to' => 10, 'range_price' => '10.00'],
                        ['id_zone' => 1, 'range_from' => 10, 'range_to' => 20, 'range_price' => '11.00'],
                        ['id_zone' => 1, 'range_from' => 20, 'range_to' => 25, 'range_price' => '12.00'],
                        ['id_zone' => 2, 'range_from' => 0, 'range_to' => 10, 'range_price' => '20.00'],
                        ['id_zone' => 2, 'range_from' => 10, 'range_to' => 20, 'range_price' => '21.00'],
                        ['id_zone' => 2, 'range_from' => 20, 'range_to' => 25, 'range_price' => '22.00'],
                    ]);
                }

                return null;
            });

        // Create a mock for CurrencyDataProviderInterface
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider
            ->method('getDefaultCurrencySymbol')
            ->willReturn('€');

        // Create a mock for ConfigurationInterface
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration
            ->method('get')
            ->with('PS_WEIGHT_UNIT')
            ->willReturn('kg');

        // Create a mock for ZoneByIdChoiceProvider
        $zonesChoiceProvider = $this->createMock(ZoneByIdChoiceProvider::class);
        $zonesChoiceProvider
            ->method('getChoices')
            ->willReturn([
                'Zone A' => 1,
                'Zone B' => 2,
                'Zone C' => 3,
            ]);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);

        $formDataProvider = new CarrierFormDataProvider(
            $queryBus,
            $this->createMock(ShopContext::class),
            $currencyDataProvider,
            $configuration,
            $zonesChoiceProvider,
            $groupDataProvider
        );
        $formData = $formDataProvider->getData(42);
        $this->assertEquals([
            'general_settings' => [
                'name' => 'Carrier name',
                'localized_delay' => [
                    1 => 'English delay',
                    2 => 'French delay',
                ],
                'active' => true,
                'grade' => 5,
                'group_access' => [1, 2, 3],
                'logo_preview' => '/img/c/45.jkg',
                'tracking_url' => 'http://track.to',
                'associated_shops' => [1, 3],
            ],
            'shipping_settings' => [
                'has_additional_handling_fee' => false,
                'is_free' => true,
                'shipping_method' => 1,
                'id_tax_rule_group' => 1,
                'range_behavior' => OutOfRangeBehavior::USE_HIGHEST_RANGE,
                'zones' => [1, 2],
                'ranges' => [
                    'data' => json_encode([
                        ['from' => 0, 'to' => 10],
                        ['from' => 10, 'to' => 20],
                        ['from' => 20, 'to' => 25],
                    ]),
                ],
                'ranges_costs' => [
                    ['zoneId' => 1, 'zoneName' => 'Zone A', 'ranges' => [
                        ['range' => '0kg - 10kg', 'from' => '0', 'to' => '10', 'price' => '10'],
                        ['range' => '10kg - 20kg', 'from' => '10', 'to' => '20', 'price' => '11'],
                        ['range' => '20kg - 25kg', 'from' => '20', 'to' => '25', 'price' => '12'],
                    ]],
                    ['zoneId' => 2, 'zoneName' => 'Zone B', 'ranges' => [
                        ['range' => '0kg - 10kg', 'from' => '0', 'to' => '10', 'price' => '20'],
                        ['range' => '10kg - 20kg', 'from' => '10', 'to' => '20', 'price' => '21'],
                        ['range' => '20kg - 25kg', 'from' => '20', 'to' => '25', 'price' => '22'],
                    ]],
                ],
            ],
            'size_weight_settings' => [
                'max_width' => 1234,
                'max_height' => 1123,
                'max_depth' => 3421,
                'max_weight' => 1657,
            ],
        ], $formData);
    }

    public function testGetDefaultData(): void
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getAssociatedShopIds')->willReturn([2, 4]);
        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->method('getAllGroupIds')
            ->willReturn([1, 2, 3]);

        $formDataProvider = new CarrierFormDataProvider(
            $this->createMock(CommandBusInterface::class),
            $shopContext,
            $this->createMock(CurrencyDataProviderInterface::class),
            $this->createMock(ConfigurationInterface::class),
            $this->createMock(ZoneByIdChoiceProvider::class),
            $groupDataProvider
        );
        $this->assertEquals([
            'general_settings' => [
                'grade' => 0,
                'associated_shops' => [2, 4],
                'group_access' => [1, 2, 3],
            ],
        ], $formDataProvider->getDefaultData());
    }
}
