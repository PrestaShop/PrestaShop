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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                    'label' => $this->trans('Quantity', 'Global', []),
                    'invalid_message' => $this->trans('This field is invalid, it must contain numeric values', 'Admin.Notifications.Error', []),
                    'required' => false,
                    'data' => 0,
                    'scale' => 0,
                ])
                ->add('amount_' . $product->getOrderDetailId(), NumberType::class, [
                    'attr' => ['max' => $product->getTotalPrice(), 'class' => 'refund-amount'],
                    'label' => sprintf(
                        "%s<br>(%s)",
                        $this->trans('Amount', 'Admin.Global', []),
                        $taxMethod
                    ),
                    'invalid_message' => $this->trans('This field is invalid, it must contain numeric values', 'Admin.Notifications.Error', []),
                    'required' => false,
                    'data' => 0,
                    'scale' => $precision,
                ]);
        }
        $builder
            ->add('shipping_amount', NumberType::class,
                [
                    'label' => $this->trans('Shipping', 'Admin.Catalog.Feature', []),
                    'invalid_message' => $this->trans('The "shipping" field must be a valid number', 'Admin.Orderscustomers.Feature', []),
                    'required' => false,
                    'scale' => $precision,
                    'data' => 0,
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
            ->add('voucher', CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Generate a voucher', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
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
                ],
            ]);
    }
}
