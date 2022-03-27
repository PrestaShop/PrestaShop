<?php

declare(strict_types=1);

namespace Tests\Unit\Adapter\Notifications;

use PrestaShop\PrestaShop\Adapter\Admin\NotificationsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\NotificationsType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class NotificationsConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * @var NotificationsConfiguration
     */
    private $notificationsConfiguration;

    private const VALID_CONFIGURATION = [
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS => true,
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS => true,
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES => true,
    ];

    private const SHOP_ID = 42;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationsConfiguration = new NotificationsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );
    }

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
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
                    ['PS_SHOW_NEW_ORDERS', null, $shopConstraint, self::VALID_CONFIGURATION[NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS]],
                    ['PS_SHOW_NEW_CUSTOMERS', null, $shopConstraint, self::VALID_CONFIGURATION[NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS]],
                    ['PS_SHOW_NEW_MESSAGES', null, $shopConstraint, self::VALID_CONFIGURATION[NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES]],
                ]
            );

        $result = $this->notificationsConfiguration->getConfiguration();

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
        $this->expectException($exception);

        $this->notificationsConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS => 'wrong_type'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, [NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES => 'wrong_type'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $res = $this->notificationsConfiguration->updateConfiguration(self::VALID_CONFIGURATION);

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
