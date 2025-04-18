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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordType extends AbstractType
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);

        $builder
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    $this->getLengthConstraint($maxLength, $minLength),
                ],
                'required' => true,
                'first_options' => [
                    'label' => $this->translator->trans('New password', [], 'Admin.Advparameters.Feature'),
                    'help' => $this->translator->trans(
                        'Password should be at least %num% characters long.',
                        [
                            '%num%' => Password::MIN_LENGTH,
                        ],
                        'Admin.Advparameters.Help'
                    ),
                    'attr' => [
                        'data-minscore' => $minScore,
                        'data-minlength' => $minLength,
                        'data-maxlength' => $maxLength,
                    ],
                ],
                'second_options' => [
                    'label' => $this->translator->trans('Confirm password', [], 'Admin.Advparameters.Feature'),
                    'help' => '',
                    'attr' => [
                        'data-invalid-password' => $this->translator->trans(
                            'The confirmation password doesn\'t match.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                        'data-minscore' => $minScore,
                        'data-minlength' => $minLength,
                        'data-maxlength' => $maxLength,
                    ],
                ],
            ])
            ->add('submit_login', SubmitType::class, [
                'label' => $this->translator->trans('Reset password', [], 'Admin.Login.Feature'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->translator->trans('Reset your password', [], 'Admin.Login.Feature'),
            'label_tag_name' => 'h4',
            'form_theme' => '@PrestaShop/Admin/Login/form_theme.html.twig',
            'attr' => [
                'id' => 'reset_password_form',
            ],
        ]);
    }

    /**
     * @param int $maxLength
     * @param int|null $minLength
     *
     * @return Length
     */
    private function getLengthConstraint(int $maxLength, ?int $minLength = null): Length
    {
        $options = [
            'max' => $maxLength,
            'maxMessage' => $this->getMaxLengthValidationMessage($maxLength),
        ];

        if (null !== $minLength) {
            $options['min'] = $minLength;
            $options['minMessage'] = $this->getMinLengthValidationMessage($minLength);
        }

        return new Length($options);
    }

    /**
     * @param int $minLength
     *
     * @return string
     */
    private function getMinLengthValidationMessage(int $minLength): string
    {
        return $this->translator->trans(
            'This field cannot be shorter than %limit% characters',
            ['%limit%' => $minLength],
            'Admin.Notifications.Error'
        );
    }

    /**
     * @param int $maxLength
     *
     * @return string
     */
    private function getMaxLengthValidationMessage(int $maxLength): string
    {
        return $this->translator->trans(
            'This field cannot be longer than %limit% characters',
            ['%limit%' => $maxLength],
            'Admin.Notifications.Error'
        );
    }
}
