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

use PrestaShop\PrestaShop\Adapter\Product\GeneralConfiguration;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GeneralConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $generalConfiguration = new GeneralConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->createSpecificPricePriorityUpdaterMock(),
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_CATALOG_MODE', false, $shopConstraint, true],
                    ['PS_CATALOG_MODE_WITH_PRICES', false, $shopConstraint, true],
                    ['PS_NB_DAYS_NEW_PRODUCT', 0, $shopConstraint, 3],
                    ['PS_PRODUCT_SHORT_DESC_LIMIT', 0, $shopConstraint, 30],
                    ['PS_QTY_DISCOUNT_ON_COMBINATION', 0, $shopConstraint, 5],
                    ['PS_FORCE_FRIENDLY_PRODUCT', false, $shopConstraint, true],
                    ['PS_PRODUCT_ACTIVATION_DEFAULT', false, $shopConstraint, true],
                    ['PS_SPECIFIC_PRICE_PRIORITIES', [], $shopConstraint, implode(';', [
                        PriorityList::PRIORITY_GROUP,
                        PriorityList::PRIORITY_SHOP,
                        PriorityList::PRIORITY_COUNTRY,
                        PriorityList::PRIORITY_CURRENCY,
                    ])],
                ]
            );

        $result = $generalConfiguration->getConfiguration();

        $this->assertSame(
            [
                'catalog_mode' => true,
                'catalog_mode_with_prices' => true,
                'new_days_number' => 3,
                'short_description_limit' => 30,
                'quantity_discount' => 5,
                'force_friendly_url' => true,
                'default_status' => true,
                'specific_price_priorities' => [
                    'id_group',
                    'id_shop',
                    'id_country',
                    'id_currency',
                ],
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
        $generalConfiguration = new GeneralConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->createSpecificPricePriorityUpdaterMock(),
        );

        $this->expectException($exception);
        $generalConfiguration->updateConfiguration($values);
    }

    public function testSuccessfulUpdate(): void
    {
        $generalConfiguration = new GeneralConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature,
            $this->createSpecificPricePriorityUpdaterMock(),
        );

        $res = $generalConfiguration->updateConfiguration([
            'catalog_mode' => true,
            'catalog_mode_with_prices' => true,
            'new_days_number' => 4,
            'short_description_limit' => 30,
            'quantity_discount' => 4,
            'force_friendly_url' => true,
            'default_status' => true,
            'specific_price_priorities' => [
                PriorityList::PRIORITY_GROUP,
                PriorityList::PRIORITY_SHOP,
                PriorityList::PRIORITY_COUNTRY,
                PriorityList::PRIORITY_CURRENCY,
            ],
        ]);

        $this->assertSame([], $res);
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

    /**
     * @return SpecificPricePriorityUpdater
     */
    protected function createSpecificPricePriorityUpdaterMock(): SpecificPricePriorityUpdater
    {
        return $this->getMockBuilder(SpecificPricePriorityUpdater::class)
            ->setMethods(['updateDefaultPriorities'])
            ->disableOriginalConstructor()
            ->getMock();
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
                    'catalog_mode' => 'wrong_type',
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => 'wrong_type',
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 'wrong_type',
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 'wrong_type',
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 'wrong_type',
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => 'wrong_type',
                    'default_status' => true,
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => 'wrong_type',
                    'specific_price_priorities' => [
                        'id_group',
                        'id_shop',
                        'id_country',
                        'id_currency',
                    ],
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'catalog_mode' => true,
                    'catalog_mode_with_prices' => true,
                    'new_days_number' => 3,
                    'short_description_limit' => 30,
                    'quantity_discount' => 5,
                    'force_friendly_url' => true,
                    'default_status' => true,
                    'specific_price_priorities' => 'wrong_type',
                ],
            ],
        ];
    }
}
