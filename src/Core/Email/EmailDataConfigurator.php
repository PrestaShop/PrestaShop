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

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;


/**
 * Class EmailDataConfigurator is responsible for configuring email data.
 */
final class EmailDataConfigurator extends AbstractMultistoreConfiguration
{

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'send_emails_to' => $this->configuration->get('PS_MAIL_EMAIL_MESSAGE'),
            'mail_method' => (int) $this->configuration->get('PS_MAIL_METHOD'),
            'mail_type' => (int) $this->configuration->get('PS_MAIL_TYPE'),
            'log_emails' => (bool) $this->configuration->get('PS_LOG_EMAILS'),
            'smtp_config' => [
                'domain' => $this->configuration->get('PS_MAIL_DOMAIN'),
                'server' => $this->configuration->get('PS_MAIL_SERVER'),
                'username' => $this->configuration->get('PS_MAIL_USER'),
                'password' => $this->configuration->get('PS_MAIL_PASSWD'),
                'encryption' => $this->configuration->get('PS_MAIL_SMTP_ENCRYPTION'),
                'port' => $this->configuration->get('PS_MAIL_SMTP_PORT'),
            ],
            'dkim_enable' => (bool) $this->configuration->get('PS_MAIL_DKIM_ENABLE'),
            'dkim_config' => [
                'domain' => (string) $this->configuration->get('PS_MAIL_DKIM_DOMAIN'),
                'selector' => (string) $this->configuration->get('PS_MAIL_DKIM_SELECTOR'),
                'key' => (string) $this->configuration->get('PS_MAIL_DKIM_KEY'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_MAIL_EMAIL_MESSAGE', 'send_emails_to', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_METHOD', 'mail_method', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_TYPE', 'mail_type', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LOG_EMAILS', 'log_emails', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_ENABLE', 'dkim_enable', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_DOMAIN', 'domain', $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_SELECTOR', 'selector', $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_KEY', 'key', $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DOMAIN', 'domain', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SERVER', 'server', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_USER', 'username', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_ENCRYPTION', 'encryption', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_PORT', 'port', $config['smtp_config'], $shopConstraint);
            $smtpPassword = (string) $config['smtp_config']['password'];

            if ('' !== $smtpPassword || !$this->configuration->get('PS_MAIL_PASSWD')) {
                $this->configuration->set('PS_MAIL_PASSWD', $smtpPassword);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['send_emails_to'],
            $config['mail_method'],
            $config['mail_type'],
            $config['log_emails'],
            $config['dkim_enable'],
            $config['dkim_config']['domain'],
            $config['dkim_config']['selector'],
            $config['dkim_config']['key'],
            $config['smtp_config']['domain'],
            $config['smtp_config']['server'],
            $config['smtp_config']['username'],
            $config['smtp_config']['encryption'],
            $config['smtp_config']['port'],
            $config['smtp_config']['password']
        );
    }
}
