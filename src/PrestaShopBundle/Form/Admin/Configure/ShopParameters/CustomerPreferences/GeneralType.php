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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Customer Settings" page.
 */
class GeneralType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('redisplay_cart_at_login', SwitchType::class, [
                'label' => $this->trans(
                    'Re-display cart at login',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'After a customer logs in, you can recall and display the content of his/her last shopping cart.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_CART_FOLLOWING',
            ])
            ->add('send_email_after_registration', SwitchType::class, [
                'label' => $this->trans(
                    'Send an email after registration',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Send an email with a summary of the account information after registration.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_CUSTOMER_CREATION_EMAIL',
            ])
            ->add('password_reset_delay', IntegerType::class, [
                'label' => $this->trans(
                    'Password reset delay',
                    'Admin.Shopparameters.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(
                        [
                            'value' => 0,
                            'message' => $this->trans('The field is invalid. Please enter a positive integer.', 'Admin.Notifications.Error'),
                        ]
                    ),
                    new Type(
                        [
                            'value' => 'numeric',
                            'message' => $this->trans('The field is invalid. Please enter a positive integer.', 'Admin.Notifications.Error'),
                        ]
                    ),
                ],
                'help' => $this->trans(
                    'Minimum time required between two requests for a password reset.',
                    'Admin.Shopparameters.Help'
                ),
                'unit' => $this->trans('minutes', 'Admin.Shopparameters.Feature'),
                'multistore_configuration_key' => 'PS_PASSWD_TIME_FRONT',
            ])
            ->add('enable_b2b_mode', SwitchType::class, [
                'label' => $this->trans(
                    'Enable B2B mode',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Activate or deactivate B2B mode. When this option is enabled, B2B features will be made available.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_B2B_ENABLE',
            ])
            ->add('ask_for_birthday', SwitchType::class, [
                'label' => $this->trans(
                    'Ask for birth date',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Display or not the birth date field.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_CUSTOMER_BIRTHDATE',
            ])
            ->add('enable_offers', SwitchType::class, [
                'label' => $this->trans(
                    'Enable partner offers',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Display or not the partner offers tick box, to receive offers from the store\'s partners.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_CUSTOMER_OPTIN',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customer_preferences_general_block';
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
