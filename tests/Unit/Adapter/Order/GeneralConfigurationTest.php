<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Order\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GeneralConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'enable_final_summary' => true,
        'enable_guest_checkout' => true,
        'disable_reordering_option' => true,
        'purchase_minimum_value' => 3.0,
        'recalculate_shipping_cost' => true,
        'allow_delayed_shipping' => true,
        'enable_tos' => true,
        'tos_cms_id' => 3,
        'enable_backorder_status' => true,
    ];

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
            $this->mockMultistoreFeature
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_FINAL_SUMMARY_ENABLED', false, $shopConstraint, true],
                    ['PS_GUEST_CHECKOUT_ENABLED', false, $shopConstraint, true],
                    ['PS_DISALLOW_HISTORY_REORDERING', false, $shopConstraint, true],
                    ['PS_PURCHASE_MINIMUM', 0, $shopConstraint, 3.0],
                    ['PS_ORDER_RECALCULATE_SHIPPING', false, $shopConstraint, true],
                    ['PS_SHIP_WHEN_AVAILABLE', false, $shopConstraint, true],
                    ['PS_CONDITIONS', false, $shopConstraint, true],
                    ['PS_CONDITIONS_CMS_ID', 0, $shopConstraint, 3],
                    ['PS_ENABLE_BACKORDER_STATUS', false, $shopConstraint, true],
                ]
            );

        $result = $generalConfiguration->getConfiguration();
        $this->assertSame(self::VALID_CONFIGURATION, $result);
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
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $generalConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_final_summary' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_guest_checkout' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['disable_reordering_option' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['purchase_minimum_value' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['recalculate_shipping_cost' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['allow_delayed_shipping' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_tos' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['tos_cms_id' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_backorder_status' => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $generalConfiguration = new GeneralConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $res = $generalConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
