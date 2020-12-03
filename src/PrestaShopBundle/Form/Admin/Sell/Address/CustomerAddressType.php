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
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $stateChoiceProvider,
        $contextCountryId
    ) {
        $this->translator = $translator;
        $this->stateChoiceProvider = $stateChoiceProvider;
        $this->contextCountryId = $contextCountryId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;
        $stateChoices = $this->stateChoiceProvider->getChoices(['id_country' => $countryId]);

        $hideStates = empty($stateChoices);

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
        } else {
            $builder->add('id_customer', HiddenType::class);
        }

        $builder->add('phone_mobile', TextType::class, [
            'required' => false,
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_PHONE_NUMBER,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_PHONE_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('dni', TextType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_DNI_LITE,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_DNI_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_DNI_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
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
                    'type' => TypedRegex::TYPE_GENERIC_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_ALIAS_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_ALIAS_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
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
                    'type' => TypedRegex::TYPE_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_FIRST_NAME_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_FIRST_NAME_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
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
                    'type' => TypedRegex::TYPE_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_LAST_NAME_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_LAST_NAME_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('company', TextType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_GENERIC_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_COMPANY_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_COMPANY_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('vat_number', TextType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_GENERIC_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_VAT_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_VAT_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
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
                    'type' => TypedRegex::TYPE_ADDRESS,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('address2', TextType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_ADDRESS,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_ADDRESS_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_ADDRESS_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
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
                    'type' => TypedRegex::TYPE_CITY_NAME,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_CITY_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_CITY_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('postcode', TextType::class, [
            'required' => false,
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
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_POSTCODE_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('id_country', CountryChoiceType::class, [
            'required' => true,
            'with_dni_attr' => true,
            'with_postcode_attr' => true,
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans(
                        'This field cannot be empty', [], 'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('id_state', ChoiceType::class, [
            'required' => true,
            'choices' => $stateChoices,
            'constraints' => [
                new AddressStateRequired([
                    'id_country' => $countryId,
                ]),
            ],
        ])
        ->add('phone', TextType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_PHONE_NUMBER,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_PHONE_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_PHONE_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ])
        ->add('other', TextareaType::class, [
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new CleanHtml(),
                new TypedRegex([
                    'type' => TypedRegex::TYPE_MESSAGE,
                ]),
                new Length([
                    'max' => AddressConstraint::MAX_OTHER_LENGTH,
                    'maxMessage' => $this->translator->trans(
                        'This field cannot be longer than %limit% characters',
                        ['%limit%' => AddressConstraint::MAX_OTHER_LENGTH],
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ]);
    }
}
