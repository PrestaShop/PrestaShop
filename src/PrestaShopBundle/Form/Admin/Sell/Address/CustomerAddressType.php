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
use PrestaShop\PrestaShop\Core\Domain\Address\Config\AddressConstraintConfiguration;
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
                    new ExistingCustomerEmail(),
                ],
            ]);
        }

        if ($this->isRequired('phone_mobile')) {
            $builder->add('phone_mobile', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_PHONE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_PHONE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ]);
        }

        $builder
            ->add('dni', TextType::class, [
                'required' => $this->isRequired('dni'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'dni',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_DNI_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_DNI_LENGTH],
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
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_ALIAS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_ALIAS_LENGTH],
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
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_FIRST_NAME_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_FIRST_NAME_LENGTH],
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
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_LAST_NAME_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_LAST_NAME_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('company', TextType::class, [
                'required' => $this->isRequired('company'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'company',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_COMPANY_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_COMPANY_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('vat_number', TextType::class, [
                'required' => $this->isRequired('vat_number'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'vat_number',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_VAT_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_VAT_LENGTH],
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
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_ADDRESS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_ADDRESS_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => $this->isRequired('address2'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'address2',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_ADDRESS_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_ADDRESS_LENGTH],
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
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_CITY_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_CITY_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('postcode', TextType::class, [
                'required' => $this->isRequired('postcode'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'postcode',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_POSTCODE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_POSTCODE_LENGTH],
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
                'required' => $this->isRequired('phone'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'phone',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_PHONE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_PHONE_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                ],
            ])
            ->add('other', TextareaType::class, [
                'required' => $this->isRequired('other'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'other',
                            'message' => $this->translator->trans(
                                'This field cannot be empty', [], 'Admin.Notifications.Error'
                            ),
                        ]
                    ),
                    new CleanHtml(),
                    new Length(
                        [
                            'max' => AddressConstraintConfiguration::MAX_OTHER_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => AddressConstraintConfiguration::MAX_OTHER_LENGTH],
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
                'validation_groups' => function () {
                    $groups = ['Default'];

                    //TODO add required fields as validation groups

                    return $groups;
                },
                'constraints' => [
                    new CustomerAddressZipCode(),
                    new CustomerAddressCountryRequiredFields(),
                ],
            ]
        );
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    private function isRequired(string $field): bool
    {
        //TODO implement required fields check
        return false;
    }
}
