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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EmailDataConfigurator is responsible for configuring email data.
 */
final class EmailDataConfigurator extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['send_emails_to', 'mail_method', 'mail_type', 'log_emails', 'dkim_enable', 'smtp_config', 'dkim_config'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'send_emails_to' => (int) $this->configuration->get('PS_MAIL_EMAIL_MESSAGE', 0, $shopConstraint),
            'mail_method' => (int) $this->configuration->get('PS_MAIL_METHOD', 0, $shopConstraint),
            'mail_type' => (int) $this->configuration->get('PS_MAIL_TYPE', 0, $shopConstraint),
            'log_emails' => (bool) $this->configuration->get('PS_LOG_EMAILS', false, $shopConstraint),
            'smtp_config' => [
                'domain' => $this->configuration->get('PS_MAIL_DOMAIN', null, $shopConstraint),
                'server' => $this->configuration->get('PS_MAIL_SERVER', null, $shopConstraint),
                'username' => $this->configuration->get('PS_MAIL_USER', null, $shopConstraint),
                'password' => $this->configuration->get('PS_MAIL_PASSWD', null, $shopConstraint),
                'encryption' => $this->configuration->get('PS_MAIL_SMTP_ENCRYPTION', null, $shopConstraint),
                'port' => $this->configuration->get('PS_MAIL_SMTP_PORT', null, $shopConstraint),
            ],
            'dkim_enable' => (bool) $this->configuration->get('PS_MAIL_DKIM_ENABLE', false, $shopConstraint),
            'dkim_config' => [
                'domain' => (string) $this->configuration->get('PS_MAIL_DKIM_DOMAIN', null, $shopConstraint),
                'selector' => (string) $this->configuration->get('PS_MAIL_DKIM_SELECTOR', null, $shopConstraint),
                'key' => (string) $this->configuration->get('PS_MAIL_DKIM_KEY', null, $shopConstraint),
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
                $this->updateConfigurationValue('PS_MAIL_PASSWD', 'password', $config['smtp_config'], $shopConstraint);
            }
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('send_emails_to', 'int')
            ->setAllowedTypes('mail_method', 'int')
            ->setAllowedTypes('mail_type', 'int')
            ->setAllowedTypes('log_emails', 'bool')
            ->setAllowedTypes('dkim_enable', 'bool')
            ->setAllowedTypes('smtp_config', 'array')
            ->setAllowedTypes('dkim_config', 'array');

        $resolver->setNormalizer('dkim_config', function (Options $options, $value) {
            return $this->getDkimResolver()->resolve($value ?? []);
        });

        $resolver->setNormalizer('smtp_config', function (Options $options, $value) {
            return $this->getSmtpResolver()->resolve($value ?? []);
        });

        return $resolver;
    }

    /**
     * @return OptionsResolver
     */
    private function getDkimResolver(): OptionsResolver
    {
        $dkimResolver = new OptionsResolver();
        $dkimResolver
            ->setRequired(['domain', 'selector', 'key'])
            ->setAllowedTypes('domain', 'string')
            ->setAllowedTypes('selector', 'string')
            ->setAllowedTypes('key', 'string');

        return $dkimResolver;
    }

    /**
     * @return OptionsResolver
     */
    private function getSmtpResolver(): OptionsResolver
    {
        $smtpResolver = new OptionsResolver();
        $smtpResolver
            ->setRequired(['domain', 'server', 'username', 'encryption', 'port', 'password'])
            ->setAllowedTypes('domain', 'string')
            ->setAllowedTypes('server', 'string')
            ->setAllowedTypes('username', 'string')
            ->setAllowedTypes('encryption', 'string')
            ->setAllowedTypes('port', 'string')
            ->setAllowedTypes('password', 'string');

        return $smtpResolver;
    }
}
