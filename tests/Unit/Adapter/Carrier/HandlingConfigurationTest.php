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

namespace Tests\Unit\Adapter\Carrier;

use PrestaShop\PrestaShop\Adapter\Carrier\HandlingConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class HandlingConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $HandlingConfiguration = new HandlingConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_SHIPPING_HANDLING', null, $shopConstraint, 2.6],
                    ['PS_SHIPPING_FREE_PRICE', null, $shopConstraint, 50.45],
                    ['PS_SHIPPING_FREE_WEIGHT', null, $shopConstraint, 80.6],
                ]
            );

        $result = $HandlingConfiguration->getConfiguration();
        $this->assertSame(
            [
                'shipping_handling_charges' => 2.6,
                'free_shipping_price' => 50.45,
                'free_shipping_weight' => 80.6,
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
        $HandlingConfiguration = new HandlingConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $HandlingConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['shipping_handling_charges' => 'wrong_value', 'free_shipping_price' => 10.5, 'free_shipping_weight' => 10.5]],
            [InvalidOptionsException::class, ['shipping_handling_charges' => 10.5, 'free_shipping_price' => 'wrong_value', 'free_shipping_weight' => 10.5]],
            [InvalidOptionsException::class, ['shipping_handling_charges' => 10.5, 'free_shipping_price' => 10.5, 'free_shipping_weight' => 'wrong_value']],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $HandlingConfiguration = new HandlingConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $HandlingConfiguration->updateConfiguration([
            'shipping_handling_charges' => 1.5,
            'free_shipping_price' => 50.600,
            'free_shipping_weight' => 75.8,
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
}
