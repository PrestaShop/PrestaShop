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
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

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
                'label' => $this->trans('Change password...', [], 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn-outline-secondary js-change-password',
                ],
            ])
            ->add('old_password', PasswordType::class, [
                'label' => $this->trans('Current password', [], 'Admin.Advparameters.Feature'),
                'required' => true,
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    $this->getLengthConstraint($maxLength, $minLength),
                ],
                'required' => true,
                'first_options' => [
                    'label' => $this->trans('New password', [], 'Admin.Advparameters.Feature'),
                    'help' => $this->trans(
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
                    'label' => $this->trans('Confirm password', [], 'Admin.Advparameters.Feature'),
                    'help' => '',
                    'attr' => [
                        'data-invalid-password' => $this->trans(
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
            ->add('generated_password', TextType::class, [
                'label' => false,
                'disabled' => true,
            ])
            ->add('generate_password_button', ButtonType::class, [
                'label' => $this->trans('Generate password', [], 'messages'),
                'attr' => [
                    'class' => 'btn-outline-secondary',
                ],
            ])
            ->add('cancel_button', ButtonType::class, [
                'label' => $this->trans('Cancel', [], 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn-outline-secondary js-change-password-cancel',
                ],
            ])
        ;
    }

    /**
     * @param int $maxLength
     * @param int|null $minLength
     *
     * @return Length
     */
    private function getLengthConstraint(int $maxLength, int $minLength = null): Length
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
