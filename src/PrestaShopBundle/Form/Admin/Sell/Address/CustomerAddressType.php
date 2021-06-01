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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressStateRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressZipCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ExistingCustomerEmail;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for address add/edit
 */
class CustomerAddressType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $stateChoiceProvider;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * CustomerAddressType constructor.
     *
     * Backwards compatibility break introduced in 1.7.8.0 due to addition of Router as mandatory constructor argument
     * as well as extension of TranslationAwareType instead of using translator as dependency.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     * @param int $contextCountryId
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider,
        $contextCountryId,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->stateChoiceProvider = $stateChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;
        $genericInvalidCharsMessage = $this->trans(
            'Invalid characters:',
            'Admin.Notifications.Info'
        ) . ' ' . TypedRegexValidator::GENERIC_NAME_CHARS;
        $stateChoices = $this->stateChoiceProvider->getChoices(['id_country' => $countryId]);

        $showStates = !empty($stateChoices);

        if (!isset($data['id_customer'])) {
            $builder->add('customer_email', EmailType::class, [
                'label' => $this->trans('Customer email', 'Admin.Orderscustomers.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new Email([
                        'message' => $this->trans('This field is invalid', 'Admin.Notifications.Error'),
                    ]),
                    new ExistingCustomerEmail(),
                ],
                'attr' => [
                    'data-customer-information-url' => $this->router->generate('admin_customer_for_address_information'),
                ],
            ]);
        } else {
            $builder->add('id_customer', HiddenType::class);
        }

        $builder->add('dni', TextType::class, [
            'label' => $this->trans('Identification number', 'Admin.Orderscustomers.Feature'),
            'help' => $this->trans(
                'The national ID card number of this person, or a unique tax identification number.',
                'Admin.Orderscustomers.Feature'
            ),
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_DNI_LITE,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_DNI_LENGTH,
                    'maxMessage' => $this->trans(
                        'This field cannot be longer than %limit% characters',
                        'Admin.Notifications.Error',
                        ['%limit%' => AddressConstraint::MAX_DNI_LENGTH]
                    ),
                ]),
            ],
        ])
            ->add('alias', TextType::class, [
                'label' => $this->trans('Address alias', 'Admin.Orderscustomers.Feature'),
                'help' => $genericInvalidCharsMessage,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_ALIAS_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_ALIAS_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => $this->trans('First name', 'Admin.Global'),
                'help' => $this->trans(
                    'Invalid characters:',
                    'Admin.Notifications.Info'
                ) . ' ' . TypedRegexValidator::NAME_CHARS,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_FIRST_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_FIRST_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => $this->trans('Last name', 'Admin.Global'),
                'help' => $this->trans(
                    'Invalid characters:',
                    'Admin.Notifications.Info'
                ) . ' ' . TypedRegexValidator::NAME_CHARS,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_LAST_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_LAST_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('company', TextType::class, [
                'label' => $this->trans('Company', 'Admin.Global'),
                'help' => $genericInvalidCharsMessage,
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_COMPANY_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_COMPANY_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('vat_number', TextType::class, [
                'label' => $this->trans('VAT number', 'Admin.Orderscustomers.Feature'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_VAT_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_VAT_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('address1', TextType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ADDRESS,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ADDRESS,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('postcode', TextType::class, [
                'required' => true,
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'empty_data' => '',
                'constraints' => [
                    new AddressZipCode([
                        'id_country' => $countryId,
                        'required' => false,
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_POST_CODE,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_POSTCODE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_POSTCODE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field is required', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CITY_NAME,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_CITY_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_CITY_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'with_dni_attr' => true,
                'with_postcode_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                ],
            ])->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => true,
                'choices' => $stateChoices,
                'constraints' => [
                    new AddressStateRequired([
                        'id_country' => $countryId,
                    ]),
                ],
                'row_attr' => [
                    'class' => 'js-address-state-select',
                ],
                'attr' => [
                    'visible' => $showStates,
                ],
            ])->add('phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_PHONE_NUMBER,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('phone_mobile', TextType::class, [
                'label' => $this->trans('Mobile phone', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_PHONE_NUMBER,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('other', TextareaType::class, [
                'required' => false,
                'label' => $this->trans('Other', 'Admin.Global'),
                'help' => $this->trans(
                    'Invalid characters:',
                    'Admin.Notifications.Info'
                ) . ' ' . TypedRegexValidator::MESSAGE_CHARS,
                'empty_data' => '',
                'constraints' => [
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_MESSAGE,
                    ]),
                    new Length([
                        'max' => AddressConstraint::MAX_OTHER_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressConstraint::MAX_OTHER_LENGTH]
                        ),
                    ]),
                ],
            ]);
    }
}
