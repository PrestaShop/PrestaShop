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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type\CarrierRangesType;
use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type\CostsZoneType;
use PrestaShopBundle\Form\Admin\Type\MultipleZoneChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TaxGroupChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

class ShippingLocationsAndCostsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly RouterInterface $router,
        private readonly ConfigurationInterface $configuration,
        private readonly CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('zones', MultipleZoneChoiceType::class, [
                'label' => $this->trans('Zones', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('Zones that the carrier can handle', 'Admin.Shipping.Help'),
                'required' => true,
                'multiple' => true,
                'external_link' => [
                    'text' => $this->trans('[1]Manage locations[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'below',
                    'href' => $this->router->generate('admin_zones_index'),
                    'attr' => [
                        'target' => '_blank',
                    ],
                ],
                'attr' => [
                    'data-placeholder' => $this->trans('Zones', 'Admin.Shipping.Feature'),
                    'class' => 'select2 js-multiple-zone-choice carrier-ranges-control',
                ],
            ])
            ->add('is_free', SwitchType::class, [
                'label' => $this->trans('Free Shipping', 'Admin.Shipping.Feature'),
                'required' => false,
            ])
            ->add('id_tax_rule_group', TaxGroupChoiceType::class, [
                'label' => $this->trans('Tax', 'Admin.Shipping.Feature'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage taxes[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'below',
                    'href' => $this->router->generate('admin_taxes_index'),
                    'attr' => [
                        'target' => '_blank',
                    ],
                ],
                'placeholder' => false,
            ])
            ->add('has_additional_handling_fee', SwitchType::class, [
                'label' => $this->trans('Handling costs', 'Admin.Shipping.Feature'),
                'required' => false,
                'label_help_box' => $this->trans('Does the carrier have additional fees', 'Admin.Shipping.Help'),
                'external_link' => [
                    'text' => $this->trans('[1]Manage handling costs[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'below',
                    'href' => $this->router->generate('admin_shipping_preferences'),
                    'attr' => [
                        'target' => '_blank',
                    ],
                ],
            ])
            ->add('shipping_method', ChoiceType::class, [
                'label' => $this->trans('Shipping costs', 'Admin.Shipping.Feature'),
                'choices' => [
                    $this->trans("Based on the order's total price", 'Admin.Shipping.Feature') => ShippingMethod::BY_PRICE,
                    $this->trans("Based on the order's total weight", 'Admin.Shipping.Feature') => ShippingMethod::BY_WEIGHT,
                ],
                'default_empty_data' => ShippingMethod::BY_PRICE,
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'data-units' => json_encode([
                        ShippingMethod::BY_PRICE => $this->currencyDataProvider->getDefaultCurrencySymbol(),
                        ShippingMethod::BY_WEIGHT => $this->configuration->get('PS_WEIGHT_UNIT'),
                    ]),
                ],
            ])
            ->add('range_behavior', ChoiceType::class, [
                'label' => $this->trans('Out of range behavior', 'Admin.Shipping.Feature'),
                'choices' => [
                    $this->trans('Apply the cost of the highest defined range', 'Admin.Shipping.Feature') => OutOfRangeBehavior::USE_HIGHEST_RANGE,
                    $this->trans('Disable carrier', 'Admin.Shipping.Feature') => OutOfRangeBehavior::DISABLED,
                ],
                'default_empty_data' => OutOfRangeBehavior::USE_HIGHEST_RANGE,
            ])
            ->add('ranges', CarrierRangesType::class, [
                'required' => false,
                'label' => $this->trans('Set ranges and prices', 'Admin.Shipping.Feature'),
                'row_attr' => [
                    'class' => 'carrier-ranges-edit-row',
                ],
            ])
            ->add('ranges_costs', CollectionType::class, [
                'prototype_name' => '__zone__',
                'entry_type' => CostsZoneType::class,
                'label' => null,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }
}
