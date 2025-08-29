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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MergeShipmentType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['products'] as $product) {
            $builder->add('product_' . $product['order_detail_id'], CheckboxType::class, [
                'label' => $product['order_detail_id'],
                'required' => false,
                'mapped' => false,
            ]);

            $builder->add('quantity_' . $product['order_detail_id'], IntegerType::class, [
                'label' => false,
                'data' => $product['quantity'],
                'attr' => [
                    'min' => 1,
                    'max' => $product['quantity'],
                ],
                'mapped' => false,
            ]);
        }

        $builder->add('merge_to_shipment', ChoiceType::class, [
            'label' => $this->translator->trans('Select shipment to merge to', [], 'Admin.Orderscustomers.Feature'),
            'choices' => $options['shipments'],
            'choice_label' => function ($shipment) {
                return $this->translator->trans(
                    'Shipment %shipment_id% - carrier %carrier_name%',
                    [
                        '%shipment_id%' => $shipment->getId(),
                        '%carrier_name%' => $shipment->getCarrierSummary()->getName(),
                    ],
                    'Admin.Orderscustomers.Feature'
                );
            },
            'choice_value' => fn ($shipment) => $shipment ? (string) $shipment->getId() : '',
            'placeholder' => $this->translator->trans('Select shipment', [], 'Admin.Orderscustomers.Feature'),
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'products' => [],
            'shipments' => [],
        ]);
    }
}
