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

namespace Tests\Unit\Adapter\Email;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Email\EmailDataConfigurator;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class EmailDataConfiguratorTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $EmailDataConfigurator = new EmailDataConfigurator($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_MAIL_EMAIL_MESSAGE', 0, $shopConstraint, 1],
                    ['PS_MAIL_METHOD', 0, $shopConstraint, 1],
                    ['PS_MAIL_TYPE', 0, $shopConstraint, 1],
                    ['PS_LOG_EMAILS', false, $shopConstraint, true],
                    ['PS_MAIL_DKIM_ENABLE', false, $shopConstraint, true],
                    ['PS_MAIL_DKIM_DOMAIN', null, $shopConstraint, 'smtp.domain.com'],
                    ['PS_MAIL_DKIM_SELECTOR', null, $shopConstraint, 'test'],
                    ['PS_MAIL_DKIM_KEY', null, $shopConstraint, 'test'],
                    ['PS_MAIL_DOMAIN', null, $shopConstraint, 'test'],
                    ['PS_MAIL_SERVER', null, $shopConstraint, 'test'],
                    ['PS_MAIL_USER', null, $shopConstraint, 'test'],
                    ['PS_MAIL_SMTP_ENCRYPTION', null, $shopConstraint, 'test'],
                    ['PS_MAIL_SMTP_PORT', null, $shopConstraint, 'test'],
                    ['PS_MAIL_PASSWD', null, $shopConstraint, 'password'],
                ]
            );

        $result = $EmailDataConfigurator->getConfiguration();
        $this->assertSame(
            [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
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
        $EmailDataConfigurator = new EmailDataConfigurator($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $EmailDataConfigurator->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, [
                'send_emails_to' => 'wrong type', // Wrong Type
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 'wrong type', // Wrong Type
                'mail_type' => 1,
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 'wrong type', // Wrong Type
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 'wrong type', // Wrong Type
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => false, // Wrong type
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => false, // Wrong type
                    'username' => 'test',
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => false, // Wrong type
                    'password' => 'password',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => false, // Wrong type
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => false, // Wrong Type
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => 'test',
                    'port' => false, // Wrong Type
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => 'wrong type', // Wrong Type
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => false, // Wrong type
                    'selector' => 'test',
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'test',
                    'selector' => false, // Wrong Type
                    'key' => 'test',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'test',
                    'server' => 'test',
                    'username' => 'test',
                    'password' => 'test',
                    'encryption' => 'test',
                    'port' => 'test',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'test',
                    'selector' => 'test',
                    'key' => false, // Wrong Type
                ],
            ]],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $EmailDataConfigurator = new EmailDataConfigurator($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $EmailDataConfigurator->updateConfiguration([
            'send_emails_to' => 1,
            'mail_method' => 1,
            'mail_type' => 1,
            'log_emails' => true,
            'smtp_config' => [
                'domain' => 'test',
                'server' => 'test',
                'username' => 'test',
                'password' => 'password',
                'encryption' => 'test',
                'port' => 'test',
            ],
            'dkim_enable' => true,
            'dkim_config' => [
                'domain' => 'smtp.domain.com',
                'selector' => 'test',
                'key' => 'test',
            ],
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
