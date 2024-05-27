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

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Back-office login form
 */
class LoginType extends AbstractType
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly ShopContext $shopContext,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => $this->translator->trans('Email address', [], 'Admin.Global'),
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('passwd', PasswordType::class, [
                'label' => $this->translator->trans('Password', [], 'Admin.Global'),
            ])
            ->add('submit_login', SubmitType::class, [
                'label' => $this->translator->trans('Log in', [], 'Admin.Login.Feature'),
            ])
            ->add('stay_logged_in', CheckboxType::class, [
                'label' => $this->translator->trans('Stay logged in', [], 'Admin.Login.Feature'),
                'required' => false,
                'external_link' => [
                    'href' => '#forgotten_password',
                    'text' => $this->translator->trans('I forgot my password', [], 'Admin.Login.Feature'),
                    'open_in_new_tab' => false,
                    'attr' => [
                        'id' => 'forgot-password-link',
                        'class' => 'show-forgot-password',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->shopContext->getName(),
            'label_tag_name' => 'h4',
            'form_theme' => '@PrestaShop/Admin/Login/form_theme.html.twig',
            'attr' => [
                'id' => 'login_form',
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
