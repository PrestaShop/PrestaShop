<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
