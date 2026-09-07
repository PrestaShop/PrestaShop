<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepositoryInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressZipCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Store\Configuration\StoreConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface as FormFormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class StoreType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        private readonly int $contextCountryId,
        private readonly bool $isMultistoreEnabled,
        private readonly UrlGeneratorInterface $router,
        private readonly CountryRepositoryInterface $countryRepository,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        $countryId = !empty($data['id_country']) ? (int) $data['id_country'] : $this->contextCountryId;

        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'required' => true,
                'constraints' => [new DefaultLanguage()],
                'options' => [
                    'constraints' => [
                        new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME]),
                        new Length([
                            'max' => StoreConstraint::MAX_NAME_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => StoreConstraint::MAX_NAME_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('address1', TranslatableType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'required' => true,
                'constraints' => [new DefaultLanguage()],
                'options' => [
                    'constraints' => $this->getAddressCommonConstraints(),
                ],
            ])
            ->add('address2', TranslatableType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
                'options' => [
                    'constraints' => $this->getAddressCommonConstraints(),
                ],
            ])
        ;

        // Added here so it keeps its position between "Address (2)" and "City"; the
        // country-dependent constraints are (re)applied by rebuildPostcodeField().
        $this->rebuildPostcodeField($builder, $countryId);

        $builder
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('City', 'Admin.Global'))]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_CITY_NAME]),
                    new Length([
                        'max' => StoreConstraint::MAX_CITY_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => StoreConstraint::MAX_CITY_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'autocomplete' => true,
                'attr' => [
                    'data-states-url' => $this->router->generate('admin_country_states'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Country', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ])
            ->add('latitude', TextType::class, [
                'label' => $this->trans('Latitude', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Store coordinates (e.g. 45.265469 or -0.265469)', 'Admin.Shopparameters.Help'),
                'constraints' => array_merge(
                    [
                        new NotBlank([
                            'message' => $this->trans(
                                'The %s field is required.',
                                'Admin.Notifications.Error',
                                [sprintf('"%s"', $this->trans('Latitude', 'Admin.Shopparameters.Feature'))]
                            ),
                        ]),
                    ],
                    $this->getCoordinateCommonConstraints()
                ),
            ])
            ->add('longitude', TextType::class, [
                'label' => $this->trans('Longitude', 'Admin.Shopparameters.Feature'),
                'constraints' => array_merge(
                    [
                        new NotBlank([
                            'message' => $this->trans(
                                'The %s field is required.',
                                'Admin.Notifications.Error',
                                [sprintf('"%s"', $this->trans('Longitude', 'Admin.Shopparameters.Feature'))]
                            ),
                        ]),
                    ],
                    $this->getCoordinateCommonConstraints()
                ),
            ])
            ->add('phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => StoreConstraint::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => StoreConstraint::MAX_PHONE_LENGTH]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_PHONE_NUMBER]),
                ],
            ])
            ->add('fax', TextType::class, [
                'label' => $this->trans('Fax', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => StoreConstraint::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => StoreConstraint::MAX_PHONE_LENGTH]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_PHONE_NUMBER]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                        'mode' => Email::VALIDATION_MODE_STRICT,
                    ]),
                    new Length([
                        'max' => StoreConstraint::MAX_EMAIL_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => StoreConstraint::MAX_EMAIL_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('note', TranslatableType::class, [
                'label' => $this->trans('Note', 'Admin.Shopparameters.Feature'),
                'type' => FormattedTextareaType::class,
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ])
            ->add('image_preview', ImagePreviewType::class, [
                'label' => $this->trans('Picture', 'Admin.Global'),
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => null,
                'required' => false,
            ])
            ->add('hours', TranslatableType::class, [
                'label' => $this->trans('Hours', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Enter opening/closing hours for each day. Format: HH:MM | HH:MM (e.g. 09:00 | 18:00). Leave blank for closed.',
                    'Admin.Shopparameters.Help'
                ),
                'type' => StoreHoursType::class,
                'required' => false,
            ])
        ;

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'required' => false,
            ]);
        }

        $this->rebuildStateField($builder, $countryId);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData() ?? [];
            $this->rebuildCountryDependentFields($event->getForm(), (int) ($data['id_country'] ?? 0));
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $submittedCountryId = (int) ($event->getData()['id_country'] ?? 0);
            // A submitted id that isn't a real country (0, negative, or simply absent from
            // ps_country) must not reach the postcode/state constraints below: building an
            // AddressZipCode constraint for a non-existent country throws CountryNotFoundException
            // from inside the validator, which runs during handleRequest(), outside the
            // controller's try/catch. The bogus id_country value itself is still submitted and
            // still correctly rejected afterwards by the field's own ChoiceType validation.
            if ($submittedCountryId <= 0 || !$this->countryExists($submittedCountryId)) {
                $submittedCountryId = $this->contextCountryId;
            }
            $this->rebuildCountryDependentFields($event->getForm(), $submittedCountryId);
        });
    }

    private function countryExists(int $countryId): bool
    {
        try {
            $this->countryRepository->assertCountryExists(new CountryId($countryId));

            return true;
        } catch (CountryNotFoundException) {
            return false;
        }
    }

    /**
     * @return array<int, object>
     */
    private function getAddressCommonConstraints(): array
    {
        return [
            new TypedRegex(['type' => TypedRegex::TYPE_ADDRESS]),
            new Length([
                'max' => StoreConstraint::MAX_ADDRESS_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => StoreConstraint::MAX_ADDRESS_LENGTH]
                ),
            ]),
        ];
    }

    /**
     * Latitude/longitude reach DecimalNumber unguarded once they pass here (in
     * StoreFormDataHandler), which throws a raw InvalidArgumentException for anything it can't
     * parse as a number; matching its own accepted grammar here turns that into a normal field
     * error instead. The legacy Store ObjectModel column is 13 chars wide, hence the Length.
     *
     * @return array<int, object>
     */
    private function getCoordinateCommonConstraints(): array
    {
        return [
            new Regex([
                'pattern' => '/^[-+]?\d+(?:\.\d+(?:[eE][-+]\d+)?)?$|^[-+]?\d+[eE][-+]\d+$/',
                'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
            ]),
            new Length([
                'max' => 13,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => 13]
                ),
            ]),
        ];
    }

    /**
     * Postcode and state both depend on the selected country, so they are (re)built whenever
     * the country is known: at build time, when data is set, and again with the submitted
     * country so the zip code is validated against the country the user actually chose.
     *
     * @param FormFormInterface|FormBuilderInterface $form
     */
    private function rebuildCountryDependentFields($form, int $countryId): void
    {
        $this->rebuildPostcodeField($form, $countryId);
        $this->rebuildStateField($form, $countryId);
    }

    /**
     * @param FormFormInterface|FormBuilderInterface $form
     */
    private function rebuildPostcodeField($form, int $countryId): void
    {
        $form->add('postcode', TextType::class, [
            'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new TypedRegex(['type' => TypedRegex::TYPE_POST_CODE]),
                new Length([
                    'max' => StoreConstraint::MAX_POSTCODE_LENGTH,
                    'maxMessage' => $this->trans(
                        'This field cannot be longer than %limit% characters',
                        'Admin.Notifications.Error',
                        ['%limit%' => StoreConstraint::MAX_POSTCODE_LENGTH]
                    ),
                ]),
                new AddressZipCode([
                    'id_country' => $countryId,
                    'required' => false,
                ]),
            ],
        ]);
    }

    /**
     * @param FormFormInterface|FormBuilderInterface $form
     */
    private function rebuildStateField($form, int $countryId): void
    {
        $stateChoices = $countryId > 0 ? $this->statesChoiceProvider->getChoices(['id_country' => $countryId]) : [];
        $form->add('id_state', ChoiceType::class, [
            'label' => $this->trans('State', 'Admin.Global'),
            'required' => false,
            'choices' => $stateChoices,
            'row_attr' => ['class' => 'js-store-state-row'],
            'autocomplete' => true,
            'attr' => [
                'visible' => !empty($stateChoices),
            ],
        ]);
    }
}
