<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Shop\MaintenanceConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class MaintenanceConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $maintenanceConfiguration = new MaintenanceConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_MAINTENANCE_IP', null, $shopConstraint, 'test'],
                    ['PS_MAINTENANCE_TEXT', null, $shopConstraint, 'test'],
                    ['PS_SHOP_ENABLE', false, $shopConstraint, true],
                    ['PS_MAINTENANCE_ALLOW_ADMINS', false, $shopConstraint, false],
                ]
            );

        $result = $maintenanceConfiguration->getConfiguration();
        $this->assertSame(
            [
                'enable_shop' => true,
                'maintenance_allow_admins' => false,
                'maintenance_ip' => 'test',
                'maintenance_text' => 'test',
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
        $maintenanceConfiguration = new MaintenanceConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $maintenanceConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['enable_shop' => 'wrong_type', 'maintenance_allow_admins' => true, 'maintenance_ip' => 'test', 'maintenance_text' => ['fr' => 'test string']]],
            [InvalidOptionsException::class, ['enable_shop' => true, 'maintenance_allow_admins' => 'wrong_type', 'maintenance_ip' => 'test', 'maintenance_text' => ['fr' => 'test string']]],
            [InvalidOptionsException::class, ['enable_shop' => true, 'maintenance_allow_admins' => true, 'maintenance_ip' => ['wrong_type'], 'maintenance_text' => ['fr' => 'test string']]],
            [InvalidOptionsException::class, ['enable_shop' => true, 'maintenance_allow_admins' => true, 'maintenance_ip' => 'test', 'maintenance_text' => 'wrong_type']],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $maintenanceConfiguration = new MaintenanceConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $maintenanceConfiguration->updateConfiguration([
            'enable_shop' => true,
            'maintenance_allow_admins' => false,
            'maintenance_ip' => 'test',
            'maintenance_text' => ['fr' => 'test string'],
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
