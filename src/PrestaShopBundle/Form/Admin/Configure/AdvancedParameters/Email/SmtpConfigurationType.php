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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SmtpConfigurationType build form for SMTP data configuration.
 */
class SmtpConfigurationType extends TranslatorAwareType
{
    public const FIELD_MAIL_DOMAIN = 'domain';
    public const FIELD_MAIL_SERVER = 'server';
    public const FIELD_MAIL_USER = 'username';
    public const FIELD_MAIL_PASSWD = 'password';
    public const FIELD_MAIL_SMTP_ENCRYPTION = 'encryption';
    public const FIELD_MAIL_SMTP_PORT = 'port';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(static::FIELD_MAIL_DOMAIN, TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('Email domain name', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_DOMAIN',
                'help' => $this->trans('Fully qualified domain name (keep this field empty if you don\'t know).', 'Admin.Advparameters.Help'),
            ])
            ->add(static::FIELD_MAIL_SERVER, TextType::class, [
                'required' => false,
                'label' => $this->trans('SMTP server', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_SERVER',
                'help' => $this->trans('IP address or server name (e.g. smtp.mydomain.com).', 'Admin.Advparameters.Help'),
            ])
            ->add(static::FIELD_MAIL_USER, TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP username', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_USER',
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
            ])
            ->add(static::FIELD_MAIL_PASSWD, PasswordType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP password', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_PASSWD',
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add(static::FIELD_MAIL_SMTP_ENCRYPTION, ChoiceType::class, [
                'choices' => [
                    'None' => 'off',
                    'TLS' => 'tls',
                    'SSL' => 'ssl',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
                'label' => $this->trans('Encryption', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_SMTP_ENCRYPTION',
                'help' => $this->trans('SSL does not seem to be available on your server.', 'Admin.Advparameters.Help'),
            ])
            ->add(static::FIELD_MAIL_SMTP_PORT, TextType::class, [
                'required' => false,
                'label' => $this->trans('Port', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_SMTP_PORT',
                'help' => $this->trans('Port number to use.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
