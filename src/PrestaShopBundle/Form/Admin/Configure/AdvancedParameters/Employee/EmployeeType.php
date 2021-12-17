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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as EmployeeEmail;
use PrestaShopBundle\Form\Admin\Type\ChangePasswordType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EmployeeType defines an employee form.
 */
final class EmployeeType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $languagesChoices;

    /**
     * @var array
     */
    private $tabChoices;

    /**
     * @var array
     */
    private $profilesChoices;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var string
     */
    private $defaultAvatarUrl;

    /**
     * @param array $languagesChoices
     * @param array $tabChoices
     * @param array $profilesChoices
     * @param bool $isMultistoreFeatureActive
     * @param string $defaultAvatarUrl
     */
    public function __construct(
        array $languagesChoices,
        array $tabChoices,
        array $profilesChoices,
        $isMultistoreFeatureActive,
        $defaultAvatarUrl
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->tabChoices = $tabChoices;
        $this->profilesChoices = $profilesChoices;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultAvatarUrl = $defaultAvatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(FirstName::MAX_LENGTH),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(LastName::MAX_LENGTH),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(EmployeeEmail::MAX_LENGTH),
                    new Email([
                        'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('avatarUrl', FileType::class, [
                'required' => false,
                'attr' => [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ],
            ])
            ->add('has_enabled_gravatar', SwitchType::class, [
                'required' => false,
            ])
        ;

        if ($options['is_restricted_access']) {
            $builder->add('change_password', ChangePasswordType::class);
        } else {
            $builder->add('password', PasswordType::class, [
                'required' => !$options['is_for_editing'],
                'constraints' => [
                    $this->getLengthConstraint(Password::MAX_LENGTH, Password::MIN_LENGTH),
                ],
            ]);
        }

        $builder
            ->add('default_page', ChoiceType::class, [
                'choices' => $this->tabChoices,
            ])
            ->add('language', ChoiceType::class, [
                'choices' => $this->languagesChoices,
            ])
        ;

        if (!$options['is_restricted_access']) {
            $builder
                ->add(
                    'active',
                    SwitchType::class,
                    [
                        'required' => false,
                    ]
                )
                ->add(
                    'profile',
                    ChoiceType::class,
                    [
                        'choices' => $this->profilesChoices,
                    ]
                )
            ;

            if ($this->isMultistoreFeatureActive) {
                $builder->add('shop_association', ShopChoiceTreeType::class, [
                    'required' => false,
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['defaultAvatarUrl'] = $this->defaultAvatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // When is_restricted_access is set to true, the form will show fields differently:
                // - "Change password" field (with regeneration option) shown instead of single password input,
                // - Status switch not shown,
                // - Profile selection not shown,
                // - Shop association field is not shown.
                'is_restricted_access' => false,

                // Is this form used for editing the employee.
                'is_for_editing' => false,
            ])
            ->setAllowedTypes('is_restricted_access', 'bool')
            ->setAllowedTypes('is_for_editing', 'bool')
        ;
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

    /**
     * @return NotBlank
     */
    private function getNotBlankConstraint()
    {
        return new NotBlank([
            'message' => $this->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
        ]);
    }
}
