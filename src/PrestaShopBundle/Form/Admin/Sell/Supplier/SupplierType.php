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

namespace PrestaShopBundle\Form\Admin\Sell\Supplier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressDniRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressStateRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\Address\AddressSettings;
use PrestaShop\PrestaShop\Core\Domain\Supplier\SupplierSettings;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for supplier create/edit actions (Sell > Catalog > Brands & Suppliers > Supplier)
 */
class SupplierType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $statesChoiceProvider;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param ConfigurableFormChoiceProviderInterface $statesChoiceProvider
     * @param int $contextCountryId
     * @param TranslatorInterface $translator
     * @param bool $isMultistoreEnabled
     * @param Router $router
     * @param array $locales
     */
    public function __construct(
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        TranslatorInterface $translator,
        $isMultistoreEnabled,
        Router $router,
        array $locales = []
    ) {
        parent::__construct($translator, $locales);

        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;

        $invalidCharsText = sprintf(
            '%s ' . TypedRegexValidator::CATALOG_CHARS,
            $this->trans('Invalid characters:', 'Admin.Global')
        );

        $invalidGenericNameHint = sprintf(
            '%s ' . TypedRegexValidator::GENERIC_NAME_CHARS,
            $this->trans('Invalid characters:', 'Admin.Global')
        );

        $keywordHint = sprintf(
            '%s ' . PHP_EOL . $invalidGenericNameHint,
            $this->trans(
                'To add tags, click in the field, write something, and then press the "Enter" key.',
                'Admin.Shopparameters.Help'
            ));

        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'required' => true,
                'help' => $invalidCharsText,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => SupplierSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => SupplierSettings::MAX_NAME_LENGTH]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CATALOG_NAME,
                    ]),
                ],
            ])
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'help' => $this->trans('Will appear in the list of suppliers.', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => FormattedTextareaType::class,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans(
                                '%s is invalid.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'constraints' => $this->getPhoneCommonConstraints(),
            ])
            ->add('mobile_phone', TextType::class, [
                'label' => $this->trans('Mobile phone', 'Admin.Global'),
                'required' => false,
                'constraints' => $this->getPhoneCommonConstraints(),
            ])
            ->add('address', TextType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'constraints' => $this->getAddressCommonConstraints(),
            ])
            ->add('address2', TextType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
                'constraints' => $this->getAddressCommonConstraints(),
            ])
            ->add('post_code', TextType::class, [
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_POST_CODE,
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_POST_CODE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_POST_CODE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CITY_NAME,
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_CITY_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_CITY_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'with_dni_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'attr' => [
                    'class' => 'js-supplier-country-select',
                    'data-states-url' => $this->router->generate('admin_country_states'),
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => true,
                'choices' => $this->statesChoiceProvider->getChoices(['id_country' => $countryId]),
                'constraints' => [
                    new AddressStateRequired([
                        'id_country' => $countryId,
                    ]),
                ],
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
            ])
            ->add('dni', TextType::class, [
                'label' => $this->trans('DNI', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new AddressDniRequired([
                        'required' => false,
                        'id_country' => $countryId,
                    ]),
                    new TypedRegex([
                        'type' => 'dni_lite',
                    ]),
                    new Length([
                        'max' => 16,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 16]
                        ),
                    ]),
                ],
            ])
            ->add('logo', FileType::class, [
                'label' => $this->trans('Logo', 'Admin.Global'),
                'required' => false,
                'help' => $this->trans('Upload a supplier logo from your computer.', 'Admin.Catalog.Help'),
            ])
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Global'),
                'help' => $invalidGenericNameHint,
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_TITLE_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => SupplierSettings::MAX_META_TITLE_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Global'),
                'help' => $invalidGenericNameHint,
                'type' => TextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_DESCRIPTION_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => SupplierSettings::MAX_META_DESCRIPTION_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_keyword', TranslatableType::class, [
                'label' => $this->trans('Meta keywords', 'Admin.Global'),
                'help' => $keywordHint,
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Add tag', 'Admin.Actions'),
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_KEYWORD_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => SupplierSettings::MAX_META_KEYWORD_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
                'required' => false,
            ])
        ;

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }

    /**
     * Provides reusable address constraints
     *
     * @return array
     */
    private function getAddressCommonConstraints()
    {
        return [
            new TypedRegex([
                'type' => TypedRegex::TYPE_ADDRESS,
            ]),
            new Length([
                'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH]
                ),
            ]),
        ];
    }

    /**
     * Provides reusable phone constraints
     *
     * @return array
     */
    private function getPhoneCommonConstraints()
    {
        return [
            new TypedRegex([
                'type' => TypedRegex::TYPE_PHONE_NUMBER,
            ]),
            new Length([
                'max' => AddressSettings::MAX_PHONE_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => AddressSettings::MAX_PHONE_LENGTH]
                ),
            ]),
        ];
    }
}
