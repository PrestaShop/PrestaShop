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

namespace PrestaShopBundle\Form\Admin\Sell\Supplier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Address\AddressSettings;
use PrestaShop\PrestaShop\Core\Domain\Supplier\SupplierSettings;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for supplier create/edit actions (Sell > Catalog > Brands & Suppliers > Supplier)
 */
class SupplierType extends AbstractType
{
    /**
     * @var array
     */
    private $countryChoices;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $statesChoiceProvider;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

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
     */
    public function __construct(
        array $countryChoices,
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        TranslatorInterface $translator,
        $isMultistoreEnabled
    ) {
        $this->translator = $translator;
        $this->countryChoices = $countryChoices;
        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // By default the country id for states choices is taken from context country id
        $countryIdForStateChoices = $this->contextCountryId;

        // In case custom states country id is provided it is used instead (for example if it's edit action).
        if (null !== $options['country_id']) {
            $countryIdForStateChoices = $options['country_id'];
        }

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => SupplierSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => SupplierSettings::MAX_NAME_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'catalog_name',
                    ]),
                ],
            ])
            ->add('description', TranslatableType::class, [
                'type' => TextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('mobile_phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('post_code', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'post_code',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_POST_CODE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_POST_CODE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'city_name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_CITY_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_CITY_NAME_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_country', ChoiceType::class, [
                'required' => true,
                'choices' => $this->countryChoices,
                'translation_domain' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'required' => false,
                'translation_domain' => false,
                'choices' => $this->statesChoiceProvider->getChoices([
                    'id_country' => $countryIdForStateChoices,
                ]),
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
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_TITLE_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => SupplierSettings::MAX_META_TITLE_LENGTH],
                                'Admin.Notifications.Error'
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
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_DESCRIPTION_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => SupplierSettings::MAX_META_DESCRIPTION_LENGTH],
                                'Admin.Notifications.Error'
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
                        'placeholder' => $this->translator->trans('Add tag', [], 'Admin.Actions'),
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => SupplierSettings::MAX_META_KEYWORD_LENGTH,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => SupplierSettings::MAX_META_KEYWORD_LENGTH],
                                'Admin.Notifications.Error'
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
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            // country_id for states choices provider.
            ->setDefaults([
                'country_id' => null,
            ])
            ->setAllowedTypes('country_id', ['integer', 'null'])
        ;
    }
}
