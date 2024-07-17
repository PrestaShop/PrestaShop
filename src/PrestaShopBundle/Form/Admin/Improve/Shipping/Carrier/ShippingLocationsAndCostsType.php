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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShopBundle\Form\Admin\Type\MultipleZoneChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TaxGroupChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ShippingLocationsAndCostsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('zones', MultipleZoneChoiceType::class, [
                'label' => $this->trans('Zones', 'Admin.Shipping.Feature'),
                'required' => false,
                'multiple' => true,
                'label_help_box' => $this->trans('Zones that the carrier can handle', 'Admin.Shipping.Help'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage Zones[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'prepend',
                    'href' => '',
                ],
                'attr' => [
                    'data-placeholder' => $this->trans('Zones', 'Admin.Shipping.Feature'),
                    'class' => 'select2 js-multiple-zone-choice',
                ],
            ])
            ->add('is_free', SwitchType::class, [
                'label' => $this->trans('Free Shipping', 'Admin.Shipping.Feature'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage Free Shipping[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'prepend',
                    'href' => '',
                ],
            ])
            ->add('tax', TaxGroupChoiceType::class, [
                'label' => $this->trans('Tax', 'Admin.Shipping.Feature'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage Tax[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'prepend',
                    'href' => '',
                ],
            ])
            ->add('has_additional_handling_fee', SwitchType::class, [
                'label' => $this->trans('Handling costs', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('Does the carrier have additional fees', 'Admin.Shipping.Help'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage Free Shipping[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'prepend',
                    'href' => '',
                ],
            ])
            ->add('shipping_method', ChoiceType::class, [
                'label' => $this->trans('Shipping costs', 'Admin.Shipping.Feature'),
                'choices' => [
                    $this->trans("are based on the order's total price", 'Admin.Shipping.Feature') => 0,
                    $this->trans("are based on the order's total weight", 'Admin.Shipping.Feature') => 1,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('range_behavior', ChoiceType::class, [
                'label' => $this->trans('Out of range behavior', 'Admin.Shipping.Feature'),
                'choices' => [
                    $this->trans('Apply the cost of the highest defined range', 'Admin.Shipping.Feature') => 0,
                    $this->trans('Disable carrier', 'Admin.Shipping.Feature') => 1,
                ],
            ])
        ;
    }
}
