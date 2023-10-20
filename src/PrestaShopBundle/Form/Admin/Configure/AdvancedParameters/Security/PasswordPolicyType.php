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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Security;

use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordPolicyType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'minimum_length',
                IntegerType::class,
                [
                    'label' => $this->trans('Minimum length', 'Admin.Advparameters.Feature'),
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'maximum_length',
                IntegerType::class,
                [
                    'label' => $this->trans('Maximum length', 'Admin.Advparameters.Feature'),
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'minimum_score',
                ChoiceType::class,
                [
                    'label' => $this->trans('Minimum password security score', 'Admin.Advparameters.Feature'),
                    'choices' => [
                        '0 - Extremely guessable' => PasswordPolicyConfiguration::PASSWORD_EXTREMELY_GUESSABLE,
                        '1 - Very guessable' => PasswordPolicyConfiguration::PASSWORD_VERY_GUESSABLE,
                        '2 - Somewhat guessable' => PasswordPolicyConfiguration::PASSWORD_SOMEWHAT_GUESSABLE,
                        '3 - Safely unguessable' => PasswordPolicyConfiguration::PASSWORD_SAFELY_UNGUESSABLE,
                        '4 - Very unguessable' => PasswordPolicyConfiguration::PASSWORD_VERY_UNGUESSABLE,
                    ],
                    'choice_translation_domain' => 'Admin.Advparameters.Feature',
                    'required' => true,
                ]
            );
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
        return 'security_password_policy_block';
    }
}
