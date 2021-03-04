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

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class ChangePasswordType is responsible for defining "change password" form type.
 */
class ChangePasswordType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('change_password_button', ButtonType::class, [
                'label' => false,
            ])
            ->add('old_password', PasswordType::class)
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    $this->getLengthConstraint(Password::MAX_LENGTH, Password::MIN_LENGTH),
                ],
                'first_options' => [
                    'attr' => [
                        'data-password-too-short' => $this->getMinLengthValidationMessage(Password::MIN_LENGTH),
                        'data-password-too-long' => $this->getMaxLengthValidationMessage(Password::MAX_LENGTH),
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'data-invalid-password' => $this->trans(
                            'Invalid password confirmation',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ],
                ],
            ])
            ->add('generated_password', TextType::class, [
                'label' => false,
                'disabled' => true,
            ])
            ->add('generate_password_button', ButtonType::class)
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
     * @param int $maxLength
     * @param int|null $minLength
     *
     * @return Length
     */
    private function getLengthConstraint($maxLength, $minLength = null)
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
    private function getMinLengthValidationMessage($minLength)
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
    private function getMaxLengthValidationMessage($maxLength)
    {
        return $this->trans(
            'This field cannot be longer than %limit% characters',
            ['%limit%' => $maxLength],
            'Admin.Notifications.Error'
        );
    }
}
