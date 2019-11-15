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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerAddressCountryRequiredFields;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerAddressZipCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ExistingCustomerEmail;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\RequiredFields;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\EventSubscriber\CustomerAddressFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for address add/edit
 */
class CustomerAddressType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $stateChoiceProvider;

    private $requiredFieldsProvider;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider
    ) {
        $this->translator = $translator;
        $this->stateChoiceProvider = $stateChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $requiredFields = $data['required_fields'];

        if (!isset($data['id_customer'])) {
            $builder->add('customer_email', EmailType::class, [
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
            ]);
        }

        if ($this->isRequired(RequiredFields::REQUIRED_FIELD_PHONE_MOBILE, $requiredFields)) {
            $builder->add('phone_mobile', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'phone_number',
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
            ->add('dni', TextType::class, [
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_DNI, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_DNI, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'dni_lite',
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
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'generic_name',
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
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'name',
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
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'name',
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
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_COMPANY, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_COMPANY, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'generic_name',
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
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_VAT_NUMBER, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_VAT_NUMBER, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'generic_name',
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
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'address',
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
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_ADDRESS_2, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_ADDRESS_2, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'address',
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
            ->add('city', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'city_name',
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
            ->add('postcode', TextType::class, [
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_POST_CODE, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_POST_CODE, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'post_code',
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
            ->add('id_country', CountryChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_PHONE, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_PHONE, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'phone_number',
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
            ])
            ->add('other', TextareaType::class, [
                'required' => $this->isRequired(RequiredFields::REQUIRED_FIELD_OTHER, $requiredFields),
                'constraints' => [
                    $this->getNotBlackOrNull(RequiredFields::REQUIRED_FIELD_OTHER, $requiredFields),
                    new CleanHtml(),
                    new TypedRegex([
                        'type' => 'message',
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

        $builder->addEventSubscriber(new CustomerAddressFormSubscriber($this->stateChoiceProvider));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'constraints' => [
                    new CustomerAddressZipCode(),
                    new CustomerAddressCountryRequiredFields(),
                ],
            ]
        );
    }

    /**
     * @param string $field
     * @param array $requiredFields
     *
     * @return NotBlank|null
     */
    private function getNotBlackOrNull(string $field, array $requiredFields): ?NotBlank
    {
        return $this->isRequired($field, $requiredFields) ?
            new NotBlank(
                [
                    'message' => $this->translator->trans(
                        'This field cannot be empty', [], 'Admin.Notifications.Error'
                    ),
                ]
            ) : null;
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
