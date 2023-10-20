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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Currencies;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CurrencyType
 */
class CurrencyType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $allCurrencies;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $allCurrencies
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $allCurrencies,
        $isShopFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->allCurrencies = $allCurrencies;
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $newCurrency = !isset($options['data']['id']);
        $unofficialCurrency = isset($options['data']['unofficial']) ? (bool) $options['data']['unofficial'] : false;
        if ($newCurrency) {
            $builder
                ->add('selected_iso_code', ChoiceType::class, [
                    'label' => $this->trans('Select a currency', 'Admin.International.Feature'),
                    'help' => $this->trans(
                        'By default, PrestaShop comes with a list of official currencies. If you want to use a local currency, you will have to add it manually. For example, to accept the Iranian Toman on your store, you need to create it before.',
                        'Admin.International.Help'
                    ),
                    'choices' => $this->allCurrencies,
                    'choice_translation_domain' => false,
                    'required' => false,
                    'placeholder' => '--',
                    'attr' => [
                        'data-toggle' => 'select2',
                        'data-minimumResultsForSearch' => '1',
                    ],
                ])
                ->add('unofficial', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->trans('Create an alternative currency', 'Admin.International.Feature'),
                    'attr' => [
                        'material_design' => true,
                    ],
                ])
            ;
        } else {
            $builder
                ->add('unofficial', HiddenType::class, [
                    'required' => false,
                ])
            ;
        }
        $isoCodeAttrs = [];
        if (!$newCurrency && !$unofficialCurrency) {
            $isoCodeAttrs['readonly'] = 1;
        }

        $builder
            ->add('names', TranslatableType::class, [
                'label' => $this->trans('Currency name', 'Admin.International.Feature'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 255,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 255]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('symbols', TranslatableType::class, [
                'label' => $this->trans(
                    'Symbol',
                    'Admin.International.Feature'
                ),
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 255,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 255]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('iso_code', TextType::class, [
                'attr' => $isoCodeAttrs,
                'label' => $this->trans(
                  'ISO code',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'ISO 4217 code (e.g. USD for Dollars, EUR for Euros, etc.)',
                    'Admin.International.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('ISO code', 'Admin.International.Feature')),
                            ]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CURRENCY_ISO_CODE,
                    ]),
                ],
            ])
            ->add('exchange_rate', NumberType::class, [
                'label' => $this->trans(
                    'Exchange rate',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'Exchange rates are calculated from one unit of your store\'s default currency. For example, if the default currency is euros and your chosen currency is dollars, type "1.20" (1&euro; = $1.20).',
                    'Admin.International.Help'
                ),
                'scale' => 6,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Exchange rate', 'Admin.International.Feature')),
                            ]
                        ),
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => $this->trans(
                            'This value should be greater than %value%',
                            'Admin.Notifications.Error',
                            [
                                '%value%' => 0,
                            ]
                        ),
                    ]),
                ],
                'invalid_message' => $this->trans(
                    'This field is invalid, it must contain numeric values',
                    'Admin.Notifications.Error'
                ),
            ])
            ->add('precision', IntegerType::class, [
                'label' => $this->trans(
                    'Decimals',
                    'Admin.International.Feature'
                ),
                'constraints' => [
                    new Type([
                        'type' => 'integer',
                        'message' => $this->trans('This field is invalid', 'Admin.Notifications.Error'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->trans(
                            'This value should be greater than or equal to %value%',
                            'Admin.Notifications.Error',
                            [
                                '%value%' => 0,
                            ]
                        ),
                    ]),
                    /*
                     * I added this constraint because if range is too big it causes an "out of range" error in Vue.
                     * I chose maximum precision based on this
                     * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Errors/Precision_range
                     */
                    new LessThanOrEqual([
                        'value' => 20,
                        'message' => $this->trans(
                            'This value should be less than or equal to %value%.',
                            'Admin.Notifications.Error',
                            [
                                '%value%' => 20,
                            ]
                        ),
                    ]),
                ],
                'invalid_message' => $this->trans(
                    'Please enter a positive value',
                    'Admin.Orderscustomers.Notification'
                ),
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans(
                    'Status',
                    'Admin.Global'
                ),
                'required' => false,
            ])
            ->add('transformations', TranslatableType::class, [
                'row_attr' => [
                    'class' => 'd-none',
                ],
                'type' => HiddenType::class,
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans(
                    'Store association',
                    'Admin.Global'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Store association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
