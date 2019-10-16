<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
     * @param $isShopFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $allCurrencies,
        $isShopFeatureEnabled)
    {
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
            ->add('iso_code', TextType::class, [
                'attr' => $isoCodeAttrs,
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
                ],
                'invalid_message' => $this->trans(
                    'This field is invalid, it must contain a positive integer',
                    'Admin.Notifications.Error'
                ),
            ])
            ->add('active', SwitchType::class, [
                'required' => false,
            ])
            ->add('transformations', TranslatableType::class, [
                'type' => HiddenType::class,
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Shop association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
