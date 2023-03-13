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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as DomainEmail;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Type is used to created form for customer add/edit actions
 */
class CustomerType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $genderChoices;

    /**
     * @var array
     */
    private $groupChoices;

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
     * @param array $genderChoices
     * @param array $groupChoices
     * @param array $riskChoices
     * @param bool $isB2bFeatureEnabled
     * @param bool $isPartnerOffersEnabled
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $genderChoices,
        array $groupChoices,
        array $riskChoices,
        $isB2bFeatureEnabled,
        $isPartnerOffersEnabled,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($translator, $locales);
        $this->genderChoices = $genderChoices;
        $this->groupChoices = $groupChoices;
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->riskChoices = $riskChoices;
        $this->isPartnerOffersEnabled = $isPartnerOffersEnabled;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minScore = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE);
        $maxLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH);
        $minLength = $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH);

        $builder
            ->add('gender_id', ChoiceType::class, [
                'choices' => $this->genderChoices,
                'multiple' => false,
                'expanded' => true,
                'required' => false,
                'placeholder' => null,
                'label' => $this->trans('Social title', 'Admin.Global'),
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
                ],
                'help' => $this->trans(
                    'Password should be at least %length% characters long.',
                    'Admin.Notifications.Info',
                    [
                        '%length%' => Password::MIN_LENGTH,
                    ]
                ),
                'constraints' => [
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
                ],
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
                'choices' => $this->groupChoices,
            ])
            ->add('default_group_id', ChoiceType::class, [
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
                'placeholder' => null,
                'choices' => $this->groupChoices,
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
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
                ->add('ape_code', TextType::class, [
                    'label' => $this->trans('APE', 'Admin.Orderscustomers.Feature'),
                    'required' => false,
                    'constraints' => [
                        new Type([
                            'type' => 'alnum',
                            'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                        ]),
                    ],
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
            ])
            ->setAllowedTypes('is_password_required', 'bool')
        ;
    }
}
