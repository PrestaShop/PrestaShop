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

namespace Tests\Unit\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\StockConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class StockConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $stockConfiguration = new StockConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_ORDER_OUT_OF_STOCK', false, $shopConstraint, true],
                    ['PS_STOCK_MANAGEMENT', false, $shopConstraint, true],
                    ['PS_LABEL_IN_STOCK_PRODUCTS', null, $shopConstraint, 'label in stock'],
                    ['PS_LABEL_OOS_PRODUCTS_BOA', null, $shopConstraint, 'label boa'],
                    ['PS_LABEL_OOS_PRODUCTS_BOD', null, $shopConstraint, 'label bod'],
                    ['PS_LABEL_DELIVERY_TIME_AVAILABLE', null, $shopConstraint, 'label delivery time available'],
                    ['PS_LABEL_DELIVERY_TIME_OOSBOA', null, $shopConstraint, 'label delivery time oosboa'],
                    ['PS_PACK_STOCK_TYPE', 0, $shopConstraint, 5],
                    ['PS_SHOW_LABEL_OOS_LISTING_PAGES', false, $shopConstraint, true],
                    ['PS_LAST_QTIES', 0, $shopConstraint, 5],
                    ['PS_DISP_UNAVAILABLE_ATTR', false, $shopConstraint, true],
                ]
            );

        $result = $stockConfiguration->getConfiguration();

        $this->assertSame(
            [
                'allow_ordering_oos' => true,
                'stock_management' => true,
                'in_stock_label' => 'label in stock',
                'oos_allowed_backorders' => 'label boa',
                'oos_denied_backorders' => 'label bod',
                'delivery_time' => 'label delivery time available',
                'oos_delivery_time' => 'label delivery time oosboa',
                'pack_stock_management' => 5,
                'oos_show_label_listing_pages' => true,
                'display_last_quantities' => 5,
                'display_unavailable_attributes' => true,
            ],
            $result
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $pageConfiguration = new StockConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $pageConfiguration->updateConfiguration($values);
    }

    public function testSuccessfulUpdate()
    {
        $pageConfiguration = new StockConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $res = $pageConfiguration->updateConfiguration(
            [
                'allow_ordering_oos' => true,
                'stock_management' => true,
                'in_stock_label' => 'label in stock',
                'oos_allowed_backorders' => 'label boa',
                'oos_denied_backorders' => 'label bod',
                'delivery_time' => 'label delivery time available',
                'oos_delivery_time' => 'label delivery time oosboa',
                'pack_stock_management' => 5,
                'oos_show_label_listing_pages' => true,
                'display_last_quantities' => 5,
                'display_unavailable_attributes' => true,
            ]
        );

        $this->assertSame([], $res);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => 'wrong_type',
                    'stock_management' => true,
                    'in_stock_label' => 'label in stock',
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => 'wrong_type',
                    'in_stock_label' => 'label in stock',
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => false,
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => false,
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => false,
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => false,
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 'wrong type',
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => 'wrong type',
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 'wrong type',
                    'display_unavailable_attributes' => true,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'allow_ordering_oos' => true,
                    'stock_management' => true,
                    'in_stock_label' => false,
                    'oos_allowed_backorders' => 'label boa',
                    'oos_denied_backorders' => 'label bod',
                    'delivery_time' => 'label delivery time available',
                    'oos_delivery_time' => 'label delivery time oosboa',
                    'pack_stock_management' => 5,
                    'oos_show_label_listing_pages' => true,
                    'display_last_quantities' => 5,
                    'display_unavailable_attributes' => 'wrong type',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
