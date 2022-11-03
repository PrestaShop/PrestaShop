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

namespace Tests\Unit\Adapter\Customer;

use PrestaShop\PrestaShop\Adapter\Customer\CustomerConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class CustomerConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'redisplay_cart_at_login' => true,
        'send_email_after_registration' => true,
        'password_reset_delay' => 260,
        'enable_b2b_mode' => true,
        'ask_for_birthday' => true,
        'enable_offers' => true,
    ];

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $maintenanceConfiguration = new CustomerConfiguration(
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
                    ['PS_CART_FOLLOWING', false, $shopConstraint, true],
                    ['PS_CUSTOMER_CREATION_EMAIL', false, $shopConstraint, true],
                    ['PS_PASSWD_TIME_FRONT', 0, $shopConstraint, 260],
                    ['PS_B2B_ENABLE', false, $shopConstraint, true],
                    ['PS_CUSTOMER_BIRTHDATE', false, $shopConstraint, true],
                    ['PS_CUSTOMER_OPTIN', false, $shopConstraint, true],
                ]
            );

        $result = $maintenanceConfiguration->getConfiguration();
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
        $maintenanceConfiguration = new CustomerConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

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
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['redisplay_cart_at_login' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['send_email_after_registration' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['password_reset_delay' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_b2b_mode' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['ask_for_birthday' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_offers' => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $maintenanceConfiguration = new CustomerConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $res = $maintenanceConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
