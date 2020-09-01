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
use PrestaShop\PrestaShop\Core\Domain\Address\AddressSettings;
use PrestaShop\PrestaShop\Core\Domain\Supplier\SupplierSettings;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
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
     * @var array
     */
    private $countryChoices;

    /**
     * @var array
     */
    private $countryChoicesAttributes;

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
     * @param array $countryChoices
     * @param ConfigurableFormChoiceProviderInterface $statesChoiceProvider
     * @param $contextCountryId
     * @param TranslatorInterface $translator
     * @param $isMultistoreEnabled
     * @param array $locales
     */
    public function __construct(
        array $countryChoices,
        array $countryChoicesAttributes,
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        TranslatorInterface $translator,
        $isMultistoreEnabled,
        array $locales = []
    ) {
        parent::__construct($translator, $locales);

        $this->countryChoices = $countryChoices;
        $this->countryChoicesAttributes = $countryChoicesAttributes;
        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;
        $stateChoices = $this->statesChoiceProvider->getChoices(['id_country' => $countryId]);

        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', 'Admin.Notifications.Error'
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
            ->add('description', TranslateType::class, [
                'required' => false,
                'type' => FormattedTextareaType::class,
                'locales' => $this->locales,
                'hideTabs' => false,
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
                'empty_data' => '',
                'required' => false,
                'constraints' => $this->getPhoneCommonConstraints(),
            ])
            ->add('mobile_phone', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'constraints' => $this->getPhoneCommonConstraints(),
            ])
            ->add('address', TextType::class, [
                'empty_data' => '',
                'constraints' => $this->getAddressCommonConstraints(),
            ])
            ->add('address2', TextType::class, [
                'empty_data' => '',
                'required' => false,
                'constraints' => $this->getAddressCommonConstraints(),
            ])
            ->add('post_code', TextType::class, [
                'empty_data' => '',
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
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', 'Admin.Notifications.Error'
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
                'required' => true,
                'with_dni_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', 'Admin.Notifications.Error'
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
            ->add('dni', TextType::class, [
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
                'required' => false,
            ])
            ->add('meta_title', TranslatableType::class, [
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
                'required' => false,
            ])
        ;

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', 'Admin.Notifications.Error'
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
