<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Adapter\Tab\TabDataProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\EmployeeTotpVerificationCode;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as EmployeeEmail;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\ChangePasswordType;
use PrestaShopBundle\Form\Admin\Type\CustomContentType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Service\Routing\Router;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    private $profilesChoices;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param array $languagesChoices
     * @param array $profilesChoices
     * @param bool $isMultistoreFeatureActive
     * @param ConfigurationInterface $configuration
     * @param int $superAdminProfileId
     * @param Router $router
     */
    public function __construct(
        array $languagesChoices,
        array $profilesChoices,
        bool $isMultistoreFeatureActive,
        ConfigurationInterface $configuration,
        int $superAdminProfileId,
        Router $router,
        TranslatorInterface $translator,
        private readonly TabDataProvider $tabDataProvider,
        private readonly LanguageContext $languageContext,
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->profilesChoices = $profilesChoices;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->configuration = $configuration;
        $this->superAdminProfileId = $superAdminProfileId;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);

        $profileId = (int) ($builder->getData()['profile'] ?? reset($this->profilesChoices));

        $builder
            ->add('firstname', TextType::class, [
                'label' => $this->trans('First name', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(FirstName::MAX_LENGTH),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => $this->trans('Last name', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(LastName::MAX_LENGTH),
                ],
            ])
            ->add('avatarUrl', FileType::class, [
                'block_prefix' => 'avatar_url',
                'label' => $this->trans('Avatar', [], 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ],
            ])
            ->add('has_enabled_gravatar', SwitchType::class, [
                'label' => $this->trans('Enable gravatar', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', [], 'Admin.Global'),
                'constraints' => [
                    $this->getNotBlankConstraint(),
                    $this->getLengthConstraint(EmployeeEmail::MAX_LENGTH),
                    new Email([
                        'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('change_password', ChangePasswordType::class, [
                'block_prefix' => 'change_password',
            ])
            ->add('password', PasswordType::class, [
                'label' => $this->trans('Password', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Password should be at least %num% characters long.',
                    [
                        '%num%' => 8,
                    ],
                    'Admin.Advparameters.Help'
                ),
                'required' => !$options['is_for_editing'],
                'attr' => [
                    'data-minscore' => $minScore,
                    'data-minlength' => $minLength,
                    'data-maxlength' => $maxLength,
                ],
                'constraints' => [
                    new Length(
                        [
                            'max' => $maxLength,
                            'maxMessage' => $this->getMaxLengthValidationMessage($maxLength),
                            'min' => $minLength,
                            'minMessage' => $this->getMinLengthValidationMessage($minLength),
                        ]
                    ),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Language', [], 'Admin.Global'),
                'choices' => $this->languagesChoices,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Allow or deny this employee\'s access to the Admin panel.',
                    [],
                    'Admin.Advparameters.Help'
                ),
                'required' => false,
            ])
            ->add('profile', ChoiceType::class, [
                'label' => $this->trans('Role', [], 'Admin.Advparameters.Feature'),
                'attr' => [
                    'data-admin-profile' => $this->superAdminProfileId,
                    'data-get-tabs-url' => $this->router->generate('admin_employees_get_tabs'),
                ],
                'choices' => $this->profilesChoices,
            ])
            ->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', [], 'Admin.Global'),
                'help' => $this->trans(
                    'Select the stores the employee is allowed to access.',
                    [],
                    'Admin.Advparameters.Help'
                ),
                'required' => false,
            ])
            ->add('default_page', ChoiceType::class, $this->getDefaultPageOptions($profileId))
        ;

        // The default page choices depend on the selected profile. Rebuild them from the submitted
        // profile so a page that is only accessible to the newly selected role is accepted instead
        // of being rejected against the previously saved role (matches the command handler check).
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $submittedData = $event->getData();
            // The profile field is not submitted in restricted-access mode, where the role cannot change.
            if (!isset($submittedData['profile'])) {
                return;
            }

            $event->getForm()->add('default_page', ChoiceType::class, $this->getDefaultPageOptions((int) $submittedData['profile']));
        });

        if ($options['is_restricted_access']) {
            $builder
                ->remove('password')
                ->remove('active')
                ->remove('profile')
                ->remove('shop_association')
            ;

            $this->addTwoFactorFields($builder, $options);
        } else {
            $builder
                ->remove('change_password')
            ;
            if (!$this->isMultistoreFeatureActive) {
                $builder
                    ->remove('shop_association')
                ;
            }
        }
    }

    private function addTwoFactorFields(FormBuilderInterface $builder, array $options): void
    {
        if (!(bool) $this->configuration->get('PS_BACKOFFICE_2FA')) {
            return;
        }

        $twoFactorEnabled = (bool) $options['data']['two_factor_enabled'];
        $twoFactorTotpEnabled = (bool) $options['data']['two_factor_totp_enabled'];

        $builder
            ->add('two_factor_enabled', SwitchType::class, [
                'label' => $this->trans('Two-factor authentication (2FA)', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->trans('Require a one-time code at login for this employee.', [], 'Admin.Global'),
            ])
            ->add('two_factor_totp_enabled', SwitchType::class, [
                'label' => $this->trans('Use authenticator app (TOTP)', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->trans('', [], 'Admin.Global'),
            ])
        ;

        if (!$twoFactorEnabled && !$twoFactorTotpEnabled) {
            $builder
                ->add('two_factor_provisioning_uri', TextType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => $this->trans('2FA setup key', [], 'Admin.Global'),
                    'help' => $this->trans('If you cannot scan the QR code, copy this key into your authenticator app.', [], 'Admin.Global'),
                    'attr' => [
                        'readonly' => true,
                    ],
                    'data' => $options['two_factor_totp_secret'],
                ]);
        }

        $builder
            ->add('two_factor_totp_qr', CustomContentType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'template' => '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/Blocks/qr_code.html.twig',
                'data' => [
                    'qrCodeSrc' => $options['qr_code_src'],
                    'twoFactorTotpEnabled' => $twoFactorTotpEnabled,
                ],
            ])
        ;

        if (!$twoFactorTotpEnabled) {
            $builder
                ->add('two_factor_tot_verification_code', TextType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => $this->trans('Verification code (TOTP)', [], 'Admin.Global'),
                    'help' => $this->trans('Enter the 6-digit code generated by your authenticator app to confirm the activation of two-factor authentication.', [], 'Admin.Global'),
                    'constraints' => [
                        new EmployeeTotpVerificationCode(),
                    ],
                ]);
        }

        $builder
            ->add('two_factor_email_enabled', SwitchType::class, [
                'label' => $this->trans('Receive code by email', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->trans('A one-time code will be sent to the employee email address.', [], 'Admin.Global'),
            ])
        ;
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
                'qr_code_src' => '',
                'two_factor_totp_secret' => '',
            ])
            ->setAllowedTypes('is_restricted_access', 'bool')
            ->setAllowedTypes('is_for_editing', 'bool')
            ->setAllowedTypes('qr_code_src', 'string')
            ->setAllowedTypes('two_factor_totp_secret', 'string')
        ;
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

    /**
     * @return NotBlank
     */
    private function getNotBlankConstraint(): NotBlank
    {
        return new NotBlank([
            'message' => $this->trans('This field cannot be empty.', [], 'Admin.Notifications.Error'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultPageOptions(int $profileId): array
    {
        $viewableTabs = $this->tabDataProvider->getViewableTabs($profileId, $this->languageContext->getId());

        return [
            'label' => $this->trans('Default page', [], 'Admin.Advparameters.Feature'),
            'help' => $this->trans(
                'This page will be displayed just after login.',
                [],
                'Admin.Advparameters.Help'
            ),
            'autocomplete' => true,
            'autocomplete_minimum_choices' => 5,
            'choices' => $this->formatTabs($viewableTabs),
        ];
    }

    private function formatTabs(array $tabs): array
    {
        $tabChoices = [];
        foreach ($tabs as $tab) {
            if (empty($tab['children'])) {
                $tabChoices[$tab['name']] = $tab['id_tab'];
            } else {
                $tabChoices[$tab['name']] = $this->formatTabs($tab['children']);
            }
        }

        return $tabChoices;
    }
}
