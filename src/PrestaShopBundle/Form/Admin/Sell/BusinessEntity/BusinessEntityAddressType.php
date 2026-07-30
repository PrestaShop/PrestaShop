<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressStateRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressZipCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class BusinessEntityAddressType extends TranslatorAwareType
{
    public const FIELD_ALIAS = 'alias';
    public const FIELD_ADDRESS_1 = 'address1';
    public const FIELD_ADDRESS_2 = 'address2';
    public const FIELD_POSTCODE = 'postcode';
    public const FIELD_CITY = 'city';
    public const FIELD_COUNTRY_ID = 'id_country';
    public const FIELD_STATE_ID = 'id_state';
    public const FIELD_PHONE = 'phone';
    public const FIELD_PHONE_MOBILE = 'phone_mobile';

    /**
     * @param array<int, array<string, mixed>> $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $stateChoiceProvider,
        private readonly int $contextCountryId,
        private readonly RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = !empty($data[self::FIELD_COUNTRY_ID])
            ? (int) $data[self::FIELD_COUNTRY_ID]
            : $this->contextCountryId;

        $genericInvalidCharsMessage = $this->trans(
            'Invalid characters: %characters%',
            'Admin.Notifications.Info',
            ['%characters%' => TypedRegexValidator::GENERIC_NAME_CHARS]
        );

        $builder->add(self::FIELD_ALIAS, TextType::class, [
            'label' => $this->trans('Alias', 'Admin.Catalog.Feature'),
            'help' => $genericInvalidCharsMessage,
            'required' => true,
            'attr' => ['autocomplete' => 'off'],
            'constraints' => [
                new NotBlank([
                    'message' => $this->trans(
                        'This field cannot be empty.',
                        'Admin.Notifications.Error'
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
            ->add(self::FIELD_ADDRESS_1, TextType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'required' => true,
                'attr' => ['autocomplete' => 'address-line1'],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.',
                            'Admin.Notifications.Error'
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
            ->add(self::FIELD_ADDRESS_2, TextType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['autocomplete' => 'address-line2'],
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
            ->add(self::FIELD_POSTCODE, TextType::class, [
                'required' => false, // Manage by AddressZipCode and CountryPostcodeRequiredToggler object in FO
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
                'attr' => [
                    'class' => 'js-address-postcode-select',
                    'autocomplete' => 'postal-code',
                ],
                'label_attr' => [
                    'class' => 'js-address-postcode-label',
                ],
            ])
            ->add(self::FIELD_CITY, TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'required' => true,
                'attr' => ['autocomplete' => 'address-level2'],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field is required',
                            'Admin.Notifications.Error'
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
            ->add(self::FIELD_COUNTRY_ID, CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'with_postcode_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                    'class' => 'js-address-country-select',
                    'autocomplete' => 'country',
                ],
            ])
            ->add(self::FIELD_STATE_ID, ChoiceType::class, ['choices' => []])
            ->add(self::FIELD_PHONE, TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['autocomplete' => 'tel'],
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
            ->add(self::FIELD_PHONE_MOBILE, TextType::class, [
                'label' => $this->trans('Mobile phone', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['autocomplete' => 'tel'],
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
        ;

        $rebuildStateChoices = function (FormEvent $event): void {
            $data = $event->getData();
            $countryId = !empty($data[self::FIELD_COUNTRY_ID])
                ? (int) $data[self::FIELD_COUNTRY_ID]
                : $this->contextCountryId;

            $event->getForm()->add(self::FIELD_STATE_ID, ChoiceType::class, $this->getStateFieldOptions($countryId));
        };

        $builder->addEventListener(FormEvents::POST_SET_DATA, $rebuildStateChoices);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $rebuildStateChoices);
    }

    /**
     * @return array<string, mixed>
     */
    private function getStateFieldOptions(int $countryId): array
    {
        $stateChoices = $this->stateChoiceProvider->getChoices(['id_country' => $countryId]);

        return [
            'label' => $this->trans('State', 'Admin.Global'),
            'required' => false,
            'choices' => $stateChoices,
            'constraints' => [
                new AddressStateRequired([
                    'id_country' => $countryId,
                ]),
            ],
            'row_attr' => [
                'class' => 'js-address-state-block',
            ],
            'attr' => [
                'visible' => !empty($stateChoices),
                'class' => 'js-address-state-select',
                'autocomplete' => 'address-level1',
            ],
        ];
    }
}
