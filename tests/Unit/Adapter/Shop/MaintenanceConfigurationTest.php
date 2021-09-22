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

namespace Tests\Unit\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Shop\MaintenanceConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class MaintenanceConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * @var PreferencesConfiguration
     */
    private $maintenanceConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->maintenanceConfiguration = new MaintenanceConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);
    }

    /**
     * @dataProvider provideShopConstraints
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_MAINTENANCE_IP', null, $shopConstraint, 'test'],
                    ['PS_MAINTENANCE_IP', null, null, null],
                    ['PS_MAINTENANCE_TEXT', null, $shopConstraint, 'test'],
                    ['PS_MAINTENANCE_TEXT', null, null, null],
                ]
            );

        $this->mockConfiguration
            ->method('getBoolean')
            ->willReturnMap(
                [
                    ['PS_SHOP_ENABLE', false, true],
                ]
            );

        $result = $this->maintenanceConfiguration->getConfiguration();
        $this->assertSame(
            [
                'enable_shop' => true,
                'maintenance_ip' => 'test',
                'maintenance_text' => 'test',
            ],
            $result
        );
    }

    public function testUpdateConfigurationWithInvalidConfiguration(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->maintenanceConfiguration->updateConfiguration(['does_not_exist' => 'does_not_exist']);
        $this->expectException(InvalidOptionsException::class);
        $this->maintenanceConfiguration->updateConfiguration(['enable_shop' => 'wrong_type']);
        $this->expectException(InvalidOptionsException::class);
        $this->maintenanceConfiguration->updateConfiguration(['maintenance_text' => ['wrong_type']]);
        $this->expectException(InvalidOptionsException::class);
        $this->maintenanceConfiguration->updateConfiguration(['maintenance_text' => 'wrong_type']);
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(2)],
            [ShopConstraint::shopGroup(2)],
            [ShopConstraint::allShops()],
        ];
    }
}
