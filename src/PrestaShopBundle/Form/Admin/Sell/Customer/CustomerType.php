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

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as DomainEmail;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\ApeType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Type is used to created form for customer add/edit actions
 */
class CustomerType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @var array
     */
    private $riskChoices;

    /**
     * @var bool
     */
    private $isPartnerOffersEnabled;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FormCloner
     */
    protected $formCloner;
    /**
     * @var GroupByIdChoiceProvider
     */
    private $groupByIdChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param GroupByIdChoiceProvider $groupByIdChoiceProvider
     * @param array $locales
     * @param array $riskChoices
     * @param bool $isB2bFeatureEnabled
     * @param bool $isPartnerOffersEnabled
     * @param ConfigurationInterface $configuration
     * @param FormCloner $formCloner
     */
    public function __construct(
        TranslatorInterface $translator,
        GroupByIdChoiceProvider $groupByIdChoiceProvider,
        array $locales,
        array $riskChoices,
        $isB2bFeatureEnabled,
        $isPartnerOffersEnabled,
        ConfigurationInterface $configuration,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->riskChoices = $riskChoices;
        $this->isPartnerOffersEnabled = $isPartnerOffersEnabled;
        $this->configuration = $configuration;
        $this->formCloner = $formCloner;
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Initialize password strength configuration set in Security section of backoffice
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);

        /*
         * Initialize password constraints. When creating a customer, we are utilizing full constraints.
         * When editing a customer, we use only length constraints, to validate the field ONLY if something
         * was provided and the merchant actually wants to change the password.
         */
        $passwordConstraints = [
            new Length([
                'max' => Password::MAX_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => Password::MAX_LENGTH]
                ),
                'min' => Password::MIN_LENGTH,
                'minMessage' => $this->trans(
                    'This field cannot be shorter than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => Password::MIN_LENGTH]
                ),
            ]),
        ];
        if ($options['is_password_required']) {
            $passwordConstraints[] = new NotBlank([
                'message' => $this->trans('Password is required.', 'Admin.Notifications.Error'),
            ]);
        }

        // We show the guest field only when creating the customer AND guest checkout is enabled.
        if ($options['show_guest_field'] === true) {
            $builder
                ->add('is_guest', SwitchType::class, [
                    'label' => $this->trans('Guest account', 'Admin.Global'),
                    'help' => $this->trans(
                        'Quick customers with no password, who don\'t have access to the privileges of registered ones. You can create as many guests as needed using the same email. It could be helpful if you take phone call orders.',
                        'Admin.Orderscustomers.Help'
                    ),
                    'required' => false,
                ]);
        }

        $builder
            ->add('gender_id', GenderType::class, [
                'expanded' => true,
                'required' => false,
                'placeholder' => null,
            ])
            ->add('first_name', TextType::class, [
                'label' => $this->trans('First name', 'Admin.Global'),
                'help' => $this->trans(
                    'Only letters and the dot (.) character, followed by a space, are allowed.',
                    'Admin.Orderscustomers.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => FirstName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => FirstName::MAX_LENGTH]
                        ),
                    ]),
                    new CustomerName([
                        'message' => $this->trans('The %s field is invalid.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('First name', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => $this->trans('Last name', 'Admin.Global'),
                'help' => $this->trans(
                    'Only letters and the dot (.) character, followed by a space, are allowed.',
                    'Admin.Orderscustomers.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => LastName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => LastName::MAX_LENGTH]
                        ),
                    ]),
                    new CustomerName([
                        'message' => $this->trans(
                            'The %s field is invalid.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Last name', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => DomainEmail::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            ['%limit%' => DomainEmail::MAX_LENGTH]
                        ),
                    ]),
                    new Email([
                        'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => $this->trans('Password', 'Admin.Global'),
                'attr' => [
                    'data-minscore' => $minScore,
                    'data-minlength' => $minLength,
                    'data-maxlength' => $maxLength,
                    'autocomplete' => 'new-password',
                ],
                'help' => $this->trans(
                    'Password should be at least %length% characters long.',
                    'Admin.Notifications.Info',
                    [
                        '%length%' => Password::MIN_LENGTH,
                    ]
                ),
                'constraints' => $passwordConstraints,
                'required' => $options['is_password_required'],
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => $this->trans('Birthday', 'Admin.Orderscustomers.Feature'),
                'required' => false,
                'format' => 'yyyy MM dd',
                'input' => 'string',
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
                'help' => $this->trans(
                    'Enable or disable customer login.',
                    'Admin.Orderscustomers.Help'
                ),
                'required' => false,
            ])
            ->add('is_partner_offers_subscribed', SwitchType::class, [
                'label' => $this->trans('Partner offers', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans(
                    'This customer will receive your ads via email.',
                    'Admin.Orderscustomers.Help'
                ),
                'required' => false,
                'disabled' => !$this->isPartnerOffersEnabled,
            ])
            ->add('group_ids', MaterialChoiceTableType::class, [
                'label' => $this->trans('Group access', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans(
                    'Select all the groups that you would like to apply to this customer.',
                    'Admin.Orderscustomers.Help'
                ),
                'empty_data' => [],
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
            ])
            ->add('default_group_id', GroupType::class, [
                'label' => $this->trans('Default customer group', 'Admin.Orderscustomers.Feature'),
                'help' => sprintf(
                    '%s %s',
                    $this->trans(
                        'This group will be the user\'s default group.',
                        'Admin.Orderscustomers.Help'
                    ),
                    $this->trans(
                        'Only the discount for the selected group will be applied to this customer.',
                        'Admin.Orderscustomers.Help'
                    )
                ),
                'required' => false,
                'autocomplete' => true,
                'placeholder' => null,
            ])
        ;

        if ($this->isB2bFeatureEnabled) {
            $builder
                ->add('company_name', TextType::class, [
                    'label' => $this->trans('Company', 'Admin.Global'),
                    'required' => false,
                ])
                ->add('siret_code', TextType::class, [
                    'label' => $this->trans('SIRET', 'Admin.Orderscustomers.Feature'),
                    'required' => false,
                ])
                ->add('ape_code', ApeType::class, [
                    'label' => $this->trans('APE', 'Admin.Orderscustomers.Feature'),
                    'required' => false,
                ])
                ->add('website', TextType::class, [
                    'label' => $this->trans('Website', 'Admin.Orderscustomers.Feature'),
                    'required' => false,
                ])
                ->add('allowed_outstanding_amount', NumberType::class, [
                    'label' => $this->trans('Allowed outstanding amount', 'Admin.Orderscustomers.Feature'),
                    'help' => sprintf(
                        '%s 0-9',
                        $this->trans(
                            'Valid characters:',
                            'Admin.Orderscustomers.Help'
                        )
                    ),
                    'scale' => 6,
                    'required' => false,
                    'invalid_message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                ])
                ->add('max_payment_days', IntegerType::class, [
                    'label' => $this->trans('Maximum number of payment days', 'Admin.Orderscustomers.Feature'),
                    'help' => sprintf(
                        '%s 0-9',
                        $this->trans(
                            'Valid characters:',
                            'Admin.Orderscustomers.Help'
                        )
                    ),
                    'required' => false,
                    'invalid_message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    'constraints' => [
                        new Range([
                            'min' => 0,
                            'max' => Validate::MYSQL_UNSIGNED_INT_MAX,
                            'minMessage' => $this->trans(
                                '%s is invalid. Please enter an integer greater than or equal to 0.',
                                'Admin.Notifications.Error'
                            ),
                            'maxMessage' => $this->trans(
                                '%s is invalid. Please enter an integer lower than or equal to %s.',
                                'Admin.Notifications.Error',
                                [
                                    '{{ value }}',
                                    '{{ max }}',
                                ]
                            ),
                        ]),
                    ],
                ])
                ->add('risk_id', ChoiceType::class, [
                    'label' => $this->trans('Risk rating', 'Admin.Orderscustomers.Feature'),
                    'required' => false,
                    'placeholder' => null,
                    'choices' => $this->riskChoices,
                ])
            ;
        }

        // We add a listener that will make password field not required, if we want to create a guest
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $formData = $event->getData();

            // If is_guest was provided and it's yes, we make the field optional (removing the constraints)
            if (isset($formData['is_guest']) && $formData['is_guest'] == 1) {
                $form->add($this->formCloner->cloneForm($form->get('password'), [
                    'required' => false,
                    'constraints' => [],
                ]));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // password is configurable
                // so it may be optional when editing customer
                'is_password_required' => true,
                'show_guest_field' => false,
            ])
            ->setAllowedTypes('is_password_required', 'bool')
            ->setAllowedTypes('show_guest_field', 'bool')
        ;
    }
}
