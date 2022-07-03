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
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email\DkimConfigurationType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email\EmailConfigurationType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email\SmtpConfigurationType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EmailDataConfigurator is responsible for configuring email data.
 */
final class EmailDataConfigurator extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS =
    [
        EmailConfigurationType::FIELD_MAIL_EMAIL_MESSAGE,
        EmailConfigurationType::FIELD_MAIL_METHOD,
        EmailConfigurationType::FIELD_MAIL_TYPE,
        EmailConfigurationType::FIELD_LOG_EMAILS,
        EmailConfigurationType::FIELD_MAIL_DKIM_ENABLE,
        'smtp_config',
        'dkim_config',
    ];

    private const CONFIGURATION_FIELDS_DKIM =
    [
        DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN,
        DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR,
        DkimConfigurationType::FIELD_MAIL_DKIM_KEY,
        'multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN,
        'multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR,
        'multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_KEY,
    ];

    private const CONFIGURATION_FIELDS_SMTP =
    [
        SmtpConfigurationType::FIELD_MAIL_DOMAIN,
        SmtpConfigurationType::FIELD_MAIL_SERVER,
        SmtpConfigurationType::FIELD_MAIL_USER,
        SmtpConfigurationType::FIELD_MAIL_PASSWD,
        SmtpConfigurationType::FIELD_MAIL_SMTP_ENCRYPTION,
        SmtpConfigurationType::FIELD_MAIL_SMTP_PORT,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_DOMAIN,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_SERVER,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_USER,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_PASSWD,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_SMTP_ENCRYPTION,
        'multistore_' . SmtpConfigurationType::FIELD_MAIL_SMTP_PORT,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            EmailConfigurationType::FIELD_MAIL_EMAIL_MESSAGE => (int) $this->configuration->get('PS_MAIL_EMAIL_MESSAGE', 0, $shopConstraint),
            EmailConfigurationType::FIELD_MAIL_METHOD => (int) $this->configuration->get('PS_MAIL_METHOD', 0, $shopConstraint),
            EmailConfigurationType::FIELD_MAIL_TYPE => (int) $this->configuration->get('PS_MAIL_TYPE', 0, $shopConstraint),
            EmailConfigurationType::FIELD_LOG_EMAILS => (bool) $this->configuration->get('PS_LOG_EMAILS', false, $shopConstraint),
            'smtp_config' => [
                SmtpConfigurationType::FIELD_MAIL_DOMAIN => $this->configuration->get('PS_MAIL_DOMAIN', null, $shopConstraint),
                SmtpConfigurationType::FIELD_MAIL_SERVER => $this->configuration->get('PS_MAIL_SERVER', null, $shopConstraint),
                SmtpConfigurationType::FIELD_MAIL_USER => $this->configuration->get('PS_MAIL_USER', null, $shopConstraint),
                SmtpConfigurationType::FIELD_MAIL_PASSWD => $this->configuration->get('PS_MAIL_PASSWD', null, $shopConstraint),
                SmtpConfigurationType::FIELD_MAIL_SMTP_ENCRYPTION => $this->configuration->get('PS_MAIL_SMTP_ENCRYPTION', null, $shopConstraint),
                SmtpConfigurationType::FIELD_MAIL_SMTP_PORT => $this->configuration->get('PS_MAIL_SMTP_PORT', null, $shopConstraint),
            ],
            EmailConfigurationType::FIELD_MAIL_DKIM_ENABLE => (bool) $this->configuration->get('PS_MAIL_DKIM_ENABLE', false, $shopConstraint),
            'dkim_config' => [
                DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN => (string) $this->configuration->get('PS_MAIL_DKIM_DOMAIN', null, $shopConstraint),
                DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR => (string) $this->configuration->get('PS_MAIL_DKIM_SELECTOR', null, $shopConstraint),
                DkimConfigurationType::FIELD_MAIL_DKIM_KEY => (string) $this->configuration->get('PS_MAIL_DKIM_KEY', null, $shopConstraint),
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
            $this->updateConfigurationValue('PS_MAIL_EMAIL_MESSAGE', EmailConfigurationType::FIELD_MAIL_EMAIL_MESSAGE, $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_METHOD', EmailConfigurationType::FIELD_MAIL_METHOD, $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_TYPE', EmailConfigurationType::FIELD_MAIL_TYPE, $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LOG_EMAILS', EmailConfigurationType::FIELD_LOG_EMAILS, $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_ENABLE', EmailConfigurationType::FIELD_MAIL_DKIM_ENABLE, $config, $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_DOMAIN', DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN, $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_SELECTOR', DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR, $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DKIM_KEY', DkimConfigurationType::FIELD_MAIL_DKIM_KEY, $config['dkim_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_DOMAIN', 'domain', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SERVER', 'server', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_USER', 'username', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_ENCRYPTION', 'encryption', $config['smtp_config'], $shopConstraint);
            $this->updateConfigurationValue('PS_MAIL_SMTP_PORT', 'port', $config['smtp_config'], $shopConstraint);
            $smtpPassword = (string) $config['smtp_config']['password'];
            if ('' !== $smtpPassword || !$this->configuration->get('PS_MAIL_PASSWD')) {
                $this->updateConfigurationValue('PS_MAIL_PASSWD', SmtpConfigurationType::FIELD_MAIL_PASSWD, $config['smtp_config'], $shopConstraint);
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
            ->setAllowedTypes(EmailConfigurationType::FIELD_MAIL_EMAIL_MESSAGE, 'int')
            ->setAllowedTypes(EmailConfigurationType::FIELD_MAIL_METHOD, 'int')
            ->setAllowedTypes(EmailConfigurationType::FIELD_MAIL_TYPE, 'int')
            ->setAllowedTypes(EmailConfigurationType::FIELD_LOG_EMAILS, 'bool')
            ->setAllowedTypes(EmailConfigurationType::FIELD_MAIL_DKIM_ENABLE, 'bool');


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
            ->setDefined(self::CONFIGURATION_FIELDS_DKIM)
            ->setAllowedTypes(DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN, 'string')
            ->setAllowedTypes(DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR, 'string')
            ->setAllowedTypes(DkimConfigurationType::FIELD_MAIL_DKIM_KEY, 'string')
            ->setAllowedTypes('multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_DOMAIN, 'bool')
            ->setAllowedTypes('multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_SELECTOR, 'bool')
            ->setAllowedTypes('multistore_' . DkimConfigurationType::FIELD_MAIL_DKIM_KEY, 'bool');

        return $dkimResolver;
    }

    /**
     * @return OptionsResolver
     */
    private function getSmtpResolver(): OptionsResolver
    {
        $smtpResolver = new OptionsResolver();
        $smtpResolver
            ->setDefined(self::CONFIGURATION_FIELDS_SMTP)
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_DOMAIN, 'string')
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_SERVER, 'string')
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_USER, 'string')
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_SMTP_ENCRYPTION, 'string')
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_SMTP_PORT, 'string')
            ->setAllowedTypes(SmtpConfigurationType::FIELD_MAIL_PASSWD, 'string')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_DOMAIN, 'bool')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_SERVER, 'bool')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_USER, 'bool')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_SMTP_ENCRYPTION, 'bool')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_SMTP_PORT, 'bool')
            ->setAllowedTypes('multistore_' . SmtpConfigurationType::FIELD_MAIL_PASSWD, 'bool');

        return $smtpResolver;
    }
}
