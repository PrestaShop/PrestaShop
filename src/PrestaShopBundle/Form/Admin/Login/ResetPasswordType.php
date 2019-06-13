<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Builds "reset password" form
 */
class ResetPasswordType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lengthMessage = $this->trans(
            'The password is not in a valid format.',
            [],
            'Admin.Login.Notification'
        );

        $builder
            ->add('reset_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->trans(
                    'The password and its confirmation do not match. Please double check both passwords.',
                    [],
                    'Admin.Login.Notification'
                ),
                'first_options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Password', [], 'Admin.Global'),
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 5,
                            'max' => 72,
                            'minMessage' => $lengthMessage,
                            'maxMessage' => $lengthMessage,
                        ]),
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Confirm password', [], 'Admin.Login.Feature'),
                    ],
                ],
            ])
            ->add('email', HiddenType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('email')
            ->setAllowedTypes('email', 'string')
        ;
    }

    /**
     * @param int $maxLength
     * @param int|null $minLength
     *
     * @return Length
     */
    private function getLengthConstraint($maxLength)
    {
        $options = [
            'max' => $maxLength,
            'maxMessage' => $this->trans(
                'This field cannot be longer than %limit% characters',
                ['%limit%' => $maxLength],
                'Admin.Notifications.Error'
            ),
        ];

        if (null !== $minLength) {
            $options['min'] = $minLength;
            $options['minMessage'] = $this->trans(
                'This field cannot be shorter than %limit% characters',
                ['%limit%' => $minLength],
                'Admin.Notifications.Error'
            );
        }

        return new Length($options);
    }
}
