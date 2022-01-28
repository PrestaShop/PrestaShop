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

namespace Tests\Unit\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Order\GeneralConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GeneralConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;
    private const MINIMUM_PURCHASE_VALUE = 3.0;
    private const TOS_CMS_ID = 3;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $generalConfiguration = new GeneralConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

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
                    ['PS_PURCHASE_MINIMUM', null, $shopConstraint, self::MINIMUM_PURCHASE_VALUE],
                    ['PS_ORDER_RECALCULATE_SHIPPING', false, $shopConstraint, true],
                    ['PS_ALLOW_MULTISHIPPING', false, $shopConstraint, true],
                    ['PS_SHIP_WHEN_AVAILABLE', false, $shopConstraint, true],
                    ['PS_CONDITIONS', false, $shopConstraint, true],
                    ['PS_CONDITIONS_CMS_ID', null, $shopConstraint, self::TOS_CMS_ID],
                ]
            );

        $result = $generalConfiguration->getConfiguration();
        $this->assertSame(
            [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
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
        $generalConfiguration = new GeneralConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

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
            [InvalidOptionsException::class, [
                'enable_final_summary' => 'wrong_type',
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => 'wrong_type',
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => 'wrong_type',
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => 'wrong_type',
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => 'wrong_type',
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => 'wrong_type',
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => 'wrong_type',
                'enable_tos' => true,
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => 'wrong_type',
                'tos_cms_id' => self::TOS_CMS_ID,
            ]],
            [InvalidOptionsException::class, [
                'enable_final_summary' => true,
                'enable_guest_checkout' => true,
                'disable_reordering_option' => true,
                'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
                'recalculate_shipping_cost' => true,
                'allow_multishipping' => true,
                'allow_delayed_shipping' => true,
                'enable_tos' => true,
                'tos_cms_id' => 'wrong_type',
            ]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $generalConfiguration = new GeneralConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $generalConfiguration->updateConfiguration([
            'enable_final_summary' => true,
            'enable_guest_checkout' => true,
            'disable_reordering_option' => true,
            'purchase_minimum_value' => self::MINIMUM_PURCHASE_VALUE,
            'recalculate_shipping_cost' => true,
            'allow_multishipping' => true,
            'allow_delayed_shipping' => true,
            'enable_tos' => true,
            'tos_cms_id' => self::TOS_CMS_ID,
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
