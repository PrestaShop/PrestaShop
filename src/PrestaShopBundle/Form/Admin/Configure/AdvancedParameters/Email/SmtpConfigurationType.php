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
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('Email domain name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully qualified domain name (keep this field empty if you don\'t know).', 'Admin.Advparameters.Help'),
            ])
            ->add('server', TextType::class, [
                'required' => false,
                'label' => $this->trans('SMTP server', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('IP address or server name (e.g. smtp.mydomain.com).', 'Admin.Advparameters.Help'),
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP username', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => $this->trans('SMTP password', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Leave blank if not applicable.', 'Admin.Advparameters.Help'),
            ])
            ->add('encryption', ChoiceType::class, [
                'choices' => [
                    'None' => 'off',
                    'TLS' => 'tls',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
                'label' => $this->trans('Encryption', 'Admin.Advparameters.Feature'),
            ])
            ->add('port', TextType::class, [
                'required' => false,
                'label' => $this->trans('Port', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Port number to use.', 'Admin.Advparameters.Help'),
            ]);
    }
}
