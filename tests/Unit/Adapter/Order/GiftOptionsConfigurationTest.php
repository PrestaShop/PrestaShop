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

use PrestaShop\PrestaShop\Adapter\Order\GiftOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class GiftOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'enable_gift_wrapping' => true,
        'gift_wrapping_price' => 3.0,
        'gift_wrapping_tax_rules_group' => 3,
        'offer_recyclable_pack' => true,
    ];

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $giftOptionsConfiguration = new GiftOptionsConfiguration(
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
                    ['PS_GIFT_WRAPPING', false, $shopConstraint, true],
                    ['PS_GIFT_WRAPPING_PRICE', 0, $shopConstraint, 3.0],
                    ['PS_GIFT_WRAPPING_TAX_RULES_GROUP', 0, $shopConstraint, 3],
                    ['PS_RECYCLABLE_PACK', false, $shopConstraint, true],
                ]
            );

        $result = $giftOptionsConfiguration->getConfiguration();
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
        $giftOptionsConfiguration = new GiftOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $giftOptionsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_gift_wrapping' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['gift_wrapping_price' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['gift_wrapping_tax_rules_group' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['offer_recyclable_pack' => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $giftOptionsConfiguration = new GiftOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $res = $giftOptionsConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
