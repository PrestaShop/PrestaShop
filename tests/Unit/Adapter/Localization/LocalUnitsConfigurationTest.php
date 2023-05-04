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

namespace Tests\Unit\Adapter\Localization;

use PrestaShop\PrestaShop\Adapter\Localization\LocalUnitsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class LocalUnitsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $localUnitsConfiguration = new LocalUnitsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_WEIGHT_UNIT', null, $shopConstraint, 'Kg'],
                    ['PS_DISTANCE_UNIT', null, $shopConstraint, 'Km'],
                    ['PS_VOLUME_UNIT', null, $shopConstraint, 'L'],
                    ['PS_DIMENSION_UNIT', null, $shopConstraint, 'Cm'],
                ]
            );

        $result = $localUnitsConfiguration->getConfiguration();
        $this->assertSame(
            [
                'weight_unit' => 'Kg',
                'distance_unit' => 'Km',
                'volume_unit' => 'L',
                'dimension_unit' => 'Cm',
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
        $localUnitsConfiguration = new LocalUnitsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $localUnitsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, [
                'weight_unit' => true,
                'distance_unit' => 'Km',
                'volume_unit' => 'L',
                'dimension_unit' => 'Cm',
            ]],
            [InvalidOptionsException::class, [
                'weight_unit' => 'Kg',
                'distance_unit' => true,
                'volume_unit' => 'L',
                'dimension_unit' => 'Cm',
            ]],
            [InvalidOptionsException::class, [
                'weight_unit' => 'Kg',
                'distance_unit' => 'Km',
                'volume_unit' => true,
                'dimension_unit' => 'Cm',
            ]],
            [InvalidOptionsException::class, [
                'weight_unit' => 'Kg',
                'distance_unit' => 'Km',
                'volume_unit' => 'L',
                'dimension_unit' => true,
            ]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $localUnitsConfiguration = new LocalUnitsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $localUnitsConfiguration->updateConfiguration([
            'weight_unit' => 'Kg',
            'distance_unit' => 'Km',
            'volume_unit' => 'L',
            'dimension_unit' => 'Cm',
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
