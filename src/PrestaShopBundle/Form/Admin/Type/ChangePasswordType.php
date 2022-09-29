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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Password;

/**
 * Class ChangePasswordType is responsible for defining "change password" form type.
 */
class ChangePasswordType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);

        $builder
            ->add('change_password_button', ButtonType::class, [
                'label' => false,
            ])
            ->add('old_password', PasswordType::class)
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'data-minscore' => $minScore,
                        'data-minlength' => $minLength,
                        'data-maxlength' => $maxLength,
                        'autocomplete' => 'new-password',
                    ],
                ],
                'constraints' => [
                    new Password([
                        'minScore' => $minScore,
                        'minLength' => $minLength,
                        'maxLength' => $maxLength,
                        'invalidLengthMessage' => $this->trans(
                            'Password length must be between %min% and %max% characters.',
                            [
                                '%min%' => $minLength,
                                '%max%' => $maxLength,
                            ],
                            'Admin.Notifications.Error',
                        ),
                        'tooWeakMessage' => $this->trans(
                            'The password doesn\'t meet required strength requirement.',
                            [],
                            'Admin.Notifications.Error',
                        ),
                        'emptyMessage' => $this->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('cancel_button', ButtonType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    /**
     * @param int $minLength
     *
     * @return string
     */
    private function getMinLengthValidationMessage(int $minLength): string
    {
        return $this->trans(
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
        return $this->trans(
            'This field cannot be longer than %limit% characters',
            ['%limit%' => $maxLength],
            'Admin.Notifications.Error'
        );
    }
}
