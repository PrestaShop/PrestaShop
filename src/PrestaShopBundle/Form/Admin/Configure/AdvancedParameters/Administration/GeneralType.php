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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralType extends TranslatorAwareType
{
    public const FIELD_FRONT_COOKIE_LIFETIME = 'front_cookie_lifetime';
    public const FIELD_BACK_COOKIE_LIFETIME = 'back_cookie_lifetime';
    public const FIELD_COOKIE_SAMESITE = 'cookie_samesite';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_ip_address', SwitchType::class, [
                'label' => $this->trans('Check the cookie\'s IP address', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Check the IP address of the cookie in order to prevent your cookie from being stolen.', 'Admin.Advparameters.Help'),
            ])
            ->add(self::FIELD_FRONT_COOKIE_LIFETIME, IntegerType::class, [
                'label' => $this->trans('Lifetime of front office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Set the amount of hours during which the front office cookies are valid. After that amount of time, the customer will have to log in again.', 'Admin.Advparameters.Help'),
                'unit' => $this->trans('hours', 'Admin.Shopparameters.Feature'),
            ])
            ->add(self::FIELD_BACK_COOKIE_LIFETIME, IntegerType::class, [
                'label' => $this->trans('Lifetime of back office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When you access your back office and decide to stay logged in, your cookies lifetime defines your browser session. Set here the number of hours during which you want them valid before logging in again.', 'Admin.Advparameters.Help'),
                'unit' => $this->trans('hours', 'Admin.Shopparameters.Feature'),
            ])
            ->add(self::FIELD_COOKIE_SAMESITE, ChoiceType::class, [
                'label' => $this->trans('Cookie SameSite', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Allows you to declare if your cookie should be restricted to a first-party or same-site context.', 'Admin.Advparameters.Help'),
                'choices' => CookieOptions::SAMESITE_AVAILABLE_VALUES,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'administration_general_block';
    }
}
