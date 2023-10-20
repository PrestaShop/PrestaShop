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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier;

use Carrier;
use PrestaShop\PrestaShop\Adapter\Carrier\CarrierOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class CarrierOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $CarrierOptionsConfiguration = new CarrierOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_CARRIER_DEFAULT', null, $shopConstraint, 25],
                    ['PS_CARRIER_DEFAULT_SORT', null, $shopConstraint, Carrier::SORT_BY_POSITION],
                    ['PS_CARRIER_DEFAULT_ORDER', null, $shopConstraint, Carrier::SORT_BY_DESC],
                ]
            );

        $result = $CarrierOptionsConfiguration->getConfiguration();
        $this->assertSame(
            [
                'default_carrier' => 25,
                'carrier_default_order_by' => Carrier::SORT_BY_POSITION,
                'carrier_default_order_way' => Carrier::SORT_BY_DESC,
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
        $CarrierOptionsConfiguration = new CarrierOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $CarrierOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['default_carrier' => true, 'carrier_default_order_by' => Carrier::SORT_BY_POSITION, 'carrier_default_order_way' => Carrier::SORT_BY_DESC]],
            [InvalidOptionsException::class, ['default_carrier' => 25, 'carrier_default_order_by' => true, 'carrier_default_order_way' => Carrier::SORT_BY_DESC]],
            [InvalidOptionsException::class, ['default_carrier' => 25, 'carrier_default_order_by' => Carrier::SORT_BY_POSITION, 'carrier_default_order_way' => true]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $CarrierOptionsConfiguration = new CarrierOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $CarrierOptionsConfiguration->updateConfiguration([
            'default_carrier' => 26,
            'carrier_default_order_by' => Carrier::SORT_BY_POSITION,
            'carrier_default_order_way' => Carrier::SORT_BY_DESC,
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
