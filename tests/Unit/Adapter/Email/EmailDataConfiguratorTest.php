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
                    ['PS_MAIL_DKIM_SELECTOR', null, $shopConstraint, 'selector.domain.com'],
                    ['PS_MAIL_DKIM_KEY', null, $shopConstraint, '-----BEGIN RSA PRIVATE KEY-----.'],
                    ['PS_MAIL_DOMAIN', null, $shopConstraint, 'domain.com'],
                    ['PS_MAIL_SERVER', null, $shopConstraint, 'smtp.domain.com'],
                    ['PS_MAIL_USER', null, $shopConstraint, 'myusername'],
                    ['PS_MAIL_SMTP_ENCRYPTION', null, $shopConstraint, 'ssl'],
                    ['PS_MAIL_SMTP_PORT', null, $shopConstraint, '21'],
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
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
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
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 'wrong type', // Wrong Type
                'mail_type' => 1,
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 'wrong type', // Wrong Type
                'log_emails' => true,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 'wrong type', // Wrong Type
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => false, // Wrong type
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => false, // Wrong type
                    'username' => 'myusername',
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => false, // Wrong type
                    'password' => 'password',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => false, // Wrong type
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => false, // Wrong Type
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => 'ssl',
                    'port' => false, // Wrong Type
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => 'wrong type', // Wrong Type
                'dkim_config' => [
                    'domain' => 'smtp.domain.com',
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => false, // Wrong type
                    'selector' => 'selector.domain.com',
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'domain.com',
                    'selector' => false, // Wrong Type
                    'key' => '-----BEGIN RSA PRIVATE KEY-----.',
                ],
            ]],
            [InvalidOptionsException::class, [
                'send_emails_to' => 1,
                'mail_method' => 1,
                'mail_type' => 1,
                'log_emails' => 1,
                'smtp_config' => [
                    'domain' => 'domain.com',
                    'server' => 'smtp.domain.com',
                    'username' => 'myusername',
                    'password' => 'test',
                    'encryption' => 'ssl',
                    'port' => '21',
                ],
                'dkim_enable' => true,
                'dkim_config' => [
                    'domain' => 'domain.com',
                    'selector' => 'selector.domain.com',
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
                'domain' => 'domain.com',
                'server' => 'smtp.domain.com',
                'username' => 'myusername',
                'password' => 'password',
                'encryption' => 'ssl',
                'port' => '21',
            ],
            'dkim_enable' => true,
            'dkim_config' => [
                'domain' => 'smtp.domain.com',
                'selector' => 'selector.domain.com',
                'key' => '-----BEGIN RSA PRIVATE KEY-----.',
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
