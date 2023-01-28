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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CancelProductType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $products = $options['data']['products'];
        $taxMethod = $options['data']['taxMethod'];
        $precision = $options['data']['precision'];

        foreach ($products as $product) {
            $builder
                ->add('selected_' . $product->getOrderDetailId(), CheckboxType::class,
                    [
                        'required' => false,
                        'label' => false,
                        'attr' => [
                            'material_design' => true,
                        ],
                    ]
                )
                ->add('quantity_' . $product->getOrderDetailId(), NumberType::class, [
                    'attr' => ['max' => $product->getQuantity(), 'class' => 'refund-quantity'],
                    'label' => $this->trans('Quantity', 'Admin.Global', []),
                    'invalid_message' => $this->trans('This field is invalid, it must contain numeric values', 'Admin.Notifications.Error', []),
                    'required' => false,
                    'data' => 0,
                    'scale' => 0,
                    'unit' => '/ ' . $product->getQuantity(),
                ])
                ->add('amount_' . $product->getOrderDetailId(), TextType::class, [
                    'attr' => ['max' => $product->getTotalPrice(), 'class' => 'refund-amount'],
                    'label' => sprintf(
                        '%s (%s)',
                        $this->trans('Amount', 'Admin.Global', []),
                        $taxMethod
                    ),
                    'invalid_message' => $this->trans('This field is invalid, it must contain numeric values', 'Admin.Notifications.Error', []),
                    'required' => false,
                    'data' => (new DecimalNumber('0'))->toPrecision($precision),
                ]);
        }
        $builder
            ->add('shipping_amount', TextType::class,
                [
                    'label' => $this->trans('Shipping', 'Admin.Catalog.Feature', []),
                    'invalid_message' => $this->trans('The "shipping" field must be a valid number', 'Admin.Orderscustomers.Feature', []),
                    'required' => false,
                    'data' => (new DecimalNumber('0'))->toPrecision($precision),
                ]
            )
            ->add('shipping', CheckboxType::class,
                [
                    'label' => $this->trans('Shipping', 'Admin.Catalog.Feature', []),
                    'required' => false,
                    'attr' => [
                        'material_design' => true,
                    ],
                ]
            )
            ->add('restock', CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Re-stock products', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
                ]
            )
            ->add('credit_slip', CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Generate a credit slip', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
                    'data' => true,
                ]
            )
            ->add('voucher', CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Generate a voucher', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
                ]
            )
            ->add('voucher_refund_type', ChoiceType::class,
                [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        $this->trans('Product(s) price:', 'Admin.Orderscustomers.Feature') => VoucherRefundType::PRODUCT_PRICES_REFUND,
                        $this->trans('Product(s) price, excluding amount of initial voucher:', 'Admin.Orderscustomers.Feature') => VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
                    ],
                    'choice_attr' => function ($choice, $key) {
                        return [
                            'voucher-refund-type' => $choice,
                            'data-default-label' => $key,
                        ];
                    },
                    'data' => VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
                ]
            )
            ->add('cancel', SubmitType::class, [
                'label' => $this->trans('Cancel', 'Admin.Actions'),
                'attr' => [
                    'class' => 'cancel-product-element cancel-product-element-abort btn btn-outline-secondary',
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'cancel-product-element save btn btn-primary ml-3',
                    'formnovalidate' => true,
                    'data-partial-refund-label' => $this->trans('Partial refund', 'Admin.Orderscustomers.Feature'),
                    'data-standard-refund-label' => $this->trans('Standard refund', 'Admin.Orderscustomers.Feature'),
                    'data-return-product-label' => $this->trans('Return products', 'Admin.Orderscustomers.Feature'),
                    'data-cancel-label' => $this->trans('Cancel products', 'Admin.Orderscustomers.Feature'),
                ],
            ]);
    }
}
