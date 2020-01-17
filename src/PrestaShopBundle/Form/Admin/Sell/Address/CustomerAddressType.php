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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressDniRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressStateRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressZipCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ExistingCustomerEmail;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotBlankWhenRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\RequiredFields;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
class CustomerAddressType extends AbstractType
{
    private const COMMON_INVALID_CHARS = ' <>;=#{}';
    private const NAME_INVALID_CHARS = ' 0-9!<>,;?=+()@#"�{}_$%:';
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $stateChoiceProvider;

    /**
     * @var int
     */
    private $contextCountryId;

    /** @var Router */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     * @param $contextCountryId
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider,
        $contextCountryId,
        RouterInterface $router
    ) {
        $this->translator = $translator;
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
        $requiredFields = $data['required_fields'];
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;

        $commonInvalidCharsMessage = $this->translator->trans('Invalid characters:', [], 'Admin.Notifications.Info') . self::COMMON_INVALID_CHARS;

        if (!isset($data['id_customer'])) {
            $builder->add('customer_email', EmailType::class, [
                'label' => $this->translator->trans('Customer email', [],'Admin.Orderscustomers.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new Email([
                        'message' => $this->translator->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                    new ExistingCustomerEmail(),
                ],
                'attr' => [
                    'class' => 'js-manufacturer-country-select',
                    'data-customer-information-url' => $this->router->generate('admin_customer_for_address_information'),
                ],
            ]);
        } else {
            $builder->add('id_customer', HiddenType::class);
        }

        $builder
            ->add('dni', TextType::class, [
                'label' => $this->translator->trans('Identification number', [], 'Admin.Orderscustomers.Feature'),
                'help' => $this->translator->trans(
                    'The national ID card number of this person, or a unique tax identification number.',
                    [],
                    'Admin.Orderscustomers.Help'
                ),
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_DNI, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new AddressDniRequired([
                        'id_country' => $countryId,
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_DNI, $requiredFields),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_DNI_LITE,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_DNI_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_DNI_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('alias', TextType::class, [
                'label' => $this->translator->trans('Address alias', [],'Admin.Orderscustomers.Feature'),
                'help' => $commonInvalidCharsMessage,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_ALIAS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_ALIAS_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => $this->translator->trans('First name', [],'Admin.Global'),
                'help' => $this->translator->trans('Invalid characters:').self::NAME_INVALID_CHARS,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [],'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_FIRST_NAME_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_FIRST_NAME_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => $this->translator->trans('Last name', [], 'Admin.Global'),
                'help' => $this->translator->trans('Invalid characters:').self::NAME_INVALID_CHARS,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_LAST_NAME_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_LAST_NAME_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('company', TextType::class, [
                'label' => $this->translator->trans('Company', [], 'Admin.Global'),
                'help' => $commonInvalidCharsMessage,
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_COMPANY, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new NotBlankWhenRequired([
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_COMPANY, $requiredFields),
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_COMPANY_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_COMPANY_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('vat_number', TextType::class, [
                'label' => $this->translator->trans('VAT number', [], 'Admin.Orderscustomers.Feature'),
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_VAT_NUMBER, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new NotBlankWhenRequired([
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_VAT_NUMBER, $requiredFields),
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_GENERIC_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_VAT_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_VAT_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('address1', TextType::class, [
                'label' => $this->translator->trans('Address', [], 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ADDRESS,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => $this->translator->trans('Address (2)', [], 'Admin.Global'),
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_ADDRESS_2, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new NotBlankWhenRequired([
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_ADDRESS_2, $requiredFields),
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ADDRESS,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('postcode', TextType::class, [
                'label' => $this->translator->trans('Zip/postal code', [], 'Admin.Global'),
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_POST_CODE, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new AddressZipCode([
                        'id_country' => $countryId,
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_POST_CODE, $requiredFields),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_POST_CODE,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_POSTCODE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_POSTCODE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => $this->translator->trans('City', [], 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CITY_NAME,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_CITY_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_CITY_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->translator->trans('Country', [], 'Admin.Global'),
                'required' => true,
                'withDniAttr' => true,
                'withPostcodeAttr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'label' => $this->translator->trans('State', [], 'Admin.Global'),
                'required' => true,
                'choices' => $this->stateChoiceProvider->getChoices(['id_country' => $countryId]),
                'constraints' => [
                    new AddressStateRequired([
                        'id_country' => $countryId,
                    ]),
                ],
                'attr' => [
                    'class' => 'js-address-state-select',
//                    'row_attr' => ,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => $this->translator->trans('Phone', [], 'Admin.Global'),
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_PHONE, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new NotBlankWhenRequired([
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_PHONE, $requiredFields),
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_PHONE_NUMBER,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_PHONE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ]);
        if ($this->isRequired(RequiredFields::REQUIRED_FIELD_PHONE_MOBILE, $requiredFields)) {
            $builder->add('phone_mobile', TextType::class, [
                'label' => $this->translator->trans('Mobile phone', [], 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_PHONE_NUMBER,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_PHONE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ]);
        }
        $builder
            ->add('other', TextareaType::class, [
                'label' => $this->translator->trans('Other', [], 'Admin.Global'),
                'help' => $commonInvalidCharsMessage,
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_OTHER, $requiredFields),
                'empty_data' => '',
                'constraints' => [
                    new NotBlankWhenRequired([
                        'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_OTHER, $requiredFields),
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_MESSAGE,
                    ]),
                    new Length(
                        [
                            'max' => AddressConstraint::MAX_OTHER_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraint::MAX_OTHER_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ]);
    }

    /**
     * @param string $field
     * @param array $requiredFields
     *
     * @return bool
     */
    private function isRequired(string $field, array $requiredFields): bool
    {
        return in_array($field, $requiredFields);
    }
}
