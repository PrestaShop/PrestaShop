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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Combination\CombinationSettings;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\DeltaQuantityType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class BulkCombinationStockType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $stockManagementEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $stockManagementEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $stockManagementEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->stockManagementEnabled = $stockManagementEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->stockManagementEnabled) {
            $builder
                ->add('delta_quantity', DeltaQuantityType::class, [
                    'required' => false,
                    'label' => $this->trans('Edit quantity', 'Admin.Catalog.Feature'),
                    'disabling_switch' => true,
                    'disabling_switch_event' => 'combinationSwitchDeltaQuantity',
                    'disabled_value' => function (?array $data) {
                        return empty($data['quantity']) && empty($data['delta']);
                    },
                    'modify_all_shops' => true,
                ])
                ->add('fixed_quantity', IntegerType::class, [
                    'required' => false,
                    'label' => $this->trans('Edit fixed quantity', 'Admin.Catalog.Feature'),
                    'default_empty_data' => 0,
                    'disabling_switch' => true,
                    'disabling_switch_event' => 'combinationSwitchFixedQuantity',
                    'modify_all_shops' => true,
                ])
            ;
        }

        $builder
            ->add('minimal_quantity', NumberType::class, [
                'label' => $this->trans('Minimum order quantity', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                'default_empty_data' => 0,
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('stock_location', TextType::class, [
                'label' => $this->trans('Stock location', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Enter stock location', 'Admin.Catalog.Feature'),
                ],
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('low_stock_threshold', LowStockThresholdType::class, [
                'label' => false,
            ])
            ->add('available_date', DatePickerType::class, [
                'label' => $this->trans('Availability date', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('available_now_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when in stock', 'Admin.Catalog.Feature'),
                'required' => false,
                'disabling_switch' => true,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => CombinationSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => CombinationSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
                'modify_all_shops' => true,
            ])
            ->add('available_later_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans(
                    'Label when out of stock (and backorders allowed)',
                    'Admin.Catalog.Feature'
                ),
                'required' => false,
                'disabling_switch' => true,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => CombinationSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => CombinationSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
                'modify_all_shops' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Stocks', 'Admin.Catalog.Feature'),
        ]);
    }
}
