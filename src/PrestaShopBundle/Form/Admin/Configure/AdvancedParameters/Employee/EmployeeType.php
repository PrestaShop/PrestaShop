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
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EmployeeType defines an employee form.
 */
final class EmployeeType extends TranslatorAwareType
{
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
     * @var bool
     */
    private $isAddonsConnected;

    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $languagesChoices
     * @param array $tabChoices
     * @param array $profilesChoices
     * @param bool $isMultistoreFeatureActive
     * @param string $defaultAvatarUrl
     * @param bool $isAddonsConnected
     * @param int $superAdminProfileId
     * @param Router $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $languagesChoices,
        array $tabChoices,
        array $profilesChoices,
        bool $isMultistoreFeatureActive,
        string $defaultAvatarUrl,
        bool $isAddonsConnected,
        int $superAdminProfileId,
        Router $router
    ) {
        parent::__construct($translator, $locales);
        $this->languagesChoices = $languagesChoices;
        $this->tabChoices = $tabChoices;
        $this->profilesChoices = $profilesChoices;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultAvatarUrl = $defaultAvatarUrl;
        $this->isAddonsConnected = $isAddonsConnected;
        $this->superAdminProfileId = $superAdminProfileId;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => $this->trans('First name', 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(FirstName::MAX_LENGTH),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => $this->trans('Last name', 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(LastName::MAX_LENGTH),
                ],
            ])
            ->add('avatarUrl', FileType::class, [
                'label' => $this->trans('Avatar', 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ],
            ])
            ->add('has_enabled_gravatar', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Enable gravatar', 'Admin.Advparameters.Feature'),
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(EmployeeEmail::MAX_LENGTH),
                    new Email([
                        'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
        ;

        if ($options['is_restricted_access']) {
            $builder->add('change_password', ChangePasswordType::class, [
                'label' => $this->trans('Change password...', 'Admin.Actions'),
                'row_attr' => [
                    'class' => 'btn-outline-secondary js-change-password',
                ],
            ]);

            if ($options['show_addons_connect_button']) {
                if ($this->isAddonsConnected) {
                    $label = $this->trans('Sign out from PrestaShop Addons', 'Admin.Advparameters.Feature');
                    $target = '#module-modal-addons-logout';
                } else {
                    $label = $this->trans('Sign in', 'Admin.Advparameters.Feature');
                    $target = '#module-modal-addons-connect';
                }
                $builder->add(
                    'prestashop_addons',
                    AddonsConnectType::class,
                    [
                        'label' => $label,
                        'attr' => [
                            'class' => 'btn-outline-secondary',
                            'data-toggle' => 'modal',
                            'data-target' => $target,
                        ],
                    ]
                );
            }
            $builder->add('change_password', ChangePasswordType::class);
        } else {
            $builder->add('password', PasswordType::class, [
                'required' => !$options['is_for_editing'],
                'label' => $this->trans('Password', 'Admin.Global'),
                'help' => $this->trans(
                    'Password should be at least %num% characters long.',
                    'Admin.Advparameters.Help',
                    ['%num%' => Password::MIN_LENGTH]
                ),
                'constraints' => [
                    $this->getLengthConstraint(Password::MAX_LENGTH, Password::MIN_LENGTH),
                ],
            ]);
        }

        $builder
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Language', 'Admin.Global'),
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
                        'label' => $this->trans('Active', 'Admin.Global'),
                        'help' => $this->trans('Allow or disallow this employee to log in to the Admin panel.', 'Admin.Advparameters.Help'),
                    ]
                )
                ->add(
                    'profile',
                    ChoiceType::class,
                    [
                        'choices' => $this->profilesChoices,
                        'label' => $this->trans('Permission profile', 'Admin.Advparameters.Feature'),
                        'attr' => [
                            'data-admin-profile' => $this->superAdminProfileId,
                            'data-get-tabs-url' => $this->router->generate('admin_employees_get_tabs'),
                        ],
                    ]
                )
            ;
            if ($this->isMultistoreFeatureActive) {
                $builder->add('shop_association', ShopChoiceTreeType::class, [
                    'label' => $this->trans('Shop association', 'Admin.Global'),
                    'help' => $this->trans('Select the shops the employee is allowed to access.', 'Admin.Advparameters.Help'),
                    'required' => false,
                ]);
            }
        }

        $builder
            ->add('default_page', ChoiceType::class, [
                'choices' => $this->tabChoices,
                'label' => $this->trans('Default page', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('This page will be displayed just after login.', 'Admin.Advparameters.Help'),
                'attr' => [
                    'data-minimumResultsForSearch' => '7',
                    'data-toggle' => '2',
                ],
            ])
        ;
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
                'Admin.Notifications.Error',
                ['%limit%' => $maxLength]
            ),
        ];

        if (null !== $minLength) {
            $options['min'] = $minLength;
            $options['minMessage'] = $this->trans(
                'This field cannot be shorter than %limit% characters',
                'Admin.Notifications.Error',
                ['%limit%' => $minLength]
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
            'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
        ]);
    }
}
