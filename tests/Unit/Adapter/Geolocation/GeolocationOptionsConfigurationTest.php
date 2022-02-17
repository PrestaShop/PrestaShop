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

namespace Tests\Unit\Adapter\Geolocation;

use PrestaShop\PrestaShop\Adapter\Geolocation\GeolocationOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GeolocationOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $geolocationOptionsConfiguration = new GeolocationOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_GEOLOCATION_BEHAVIOR', 0, $shopConstraint, 1],
                    ['PS_GEOLOCATION_NA_BEHAVIOR', 0, $shopConstraint, 1],
                    ['PS_ALLOWED_COUNTRIES', null, $shopConstraint, 'fr;be;de'],
                ]
            );

        $result = $geolocationOptionsConfiguration->getConfiguration();
        $this->assertSame(
            [
                'geolocation_behaviour' => 1,
                'geolocation_na_behaviour' => 1,
                'geolocation_countries' => 'fr;be;de',
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
        $geolocationOptionsConfiguration = new GeolocationOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $geolocationOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['geolocation_behaviour' => 'wrong_type','geolocation_na_behaviour' => 1, 'geolocation_countries' => 'fr;be;de']],
            [InvalidOptionsException::class, ['geolocation_behaviour' => 1, 'geolocation_na_behaviour' => 'wrong_type','geolocation_countries' => 'fr;be;de']],
            [InvalidOptionsException::class, ['geolocation_behaviour' => 1, 'geolocation_na_behaviour' => 1, 'geolocation_countries' => 1]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $geolocationOptionsConfiguration = new GeolocationOptionsConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $geolocationOptionsConfiguration->updateConfiguration([
            'geolocation_behaviour' => 1,
            'geolocation_na_behaviour' => 1,
            'geolocation_countries' => 'fr;be;de',
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
