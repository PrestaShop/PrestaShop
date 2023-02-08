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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockMovementType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextPreviewType::class, [
                'label' => $this->trans('Date & Time', 'Admin.Global'),
            ])
            ->add('employee_name', TextPreviewType::class, [
                'label' => $this->trans('Employee', 'Admin.Global'),
                'preview_class' => 'employee_preview',
            ])
            ->add('type', HiddenType::class)
            // Quantity field depends on the data, then it's added via form event
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $data = $event->getData();
                $form = $event->getForm();

                // Check that data exists, during prototype rendering it will be empty
                $type = $data['type'] ?? StockMovement::EDITION_TYPE;
                $increasedQuantity = !empty($data['delta_quantity']) && $data['delta_quantity'] > 0;

                // For orders, we display the kind of movements instead of the data range
                if ($type === StockMovement::ORDERS_TYPE) {
                    $label = ProductType::TYPE_VIRTUAL === $options['product_type'] ?
                        $this->trans('Sold products', 'Admin.Catalog.Feature') :
                        $this->trans('Shipped products', 'Admin.Catalog.Feature')
                    ;

                    $dateData = $increasedQuantity ? $this->trans('Returned products', 'Admin.Catalog.Feature') : $label;
                    $event->setData(array_merge($event->getData(), [
                        'date' => $dateData,
                    ]));
                }

                $previewClasses = [
                    'stock_movement_quantity',
                ];
                $previewClasses[] = $increasedQuantity ? 'increased_quantity' : 'decreased_quantity';
                $form->add('delta_quantity', TextPreviewType::class, [
                    'label' => $this->trans('Quantity', 'Admin.Global'),
                    'preview_class' => implode(' ', $previewClasses),
                ]);
            });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        $type = $data['type'] ?? 'prototype';

        $view->vars['attr']['class'] = trim(
            sprintf(
                '%s %s-stock-movement',
                $view->vars['attr']['class'] ?? '',
                $type
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['product_type'])
            ->setAllowedTypes('product_type', 'string')
        ;
    }
}
