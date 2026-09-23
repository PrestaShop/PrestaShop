<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Customer;

use PrestaShop\PrestaShop\Adapter\Customer\CustomerConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
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
            // An unknown key is now left to the module that owns it, so what makes this input
            // invalid is that every field the class does own is missing.
            [MissingOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['redisplay_cart_at_login' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['send_email_after_registration' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['password_reset_delay' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_b2b_mode' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['ask_for_birthday' => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['enable_offers' => 'wrong_type'])],
        ];
    }

    /**
     * The Customer Settings form dispatches actionCustomerPreferencesPageForm, so a module can add its
     * own field to it; the value then arrives here with the core ones and must not be rejected. The
     * module reads it back from actionCustomerPreferencesPageSave.
     */
    public function testItAcceptsAFieldAModuleAddedToTheForm(): void
    {
        $customerConfiguration = new CustomerConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $result = $customerConfiguration->updateConfiguration(
            array_merge(self::VALID_CONFIGURATION, ['a_module_field' => 'added through the form hook'])
        );

        $this->assertSame([], $result);
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
