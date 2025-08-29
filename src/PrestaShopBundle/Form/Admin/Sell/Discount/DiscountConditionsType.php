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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShopBundle\Form\Admin\Type\EnrichedChoiceType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountConditionsType extends TranslatorAwareType
{
    public const CART_CONDITIONS = 'cart_conditions';
    public const DELIVERY_CONDITIONS = 'delivery_conditions';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountType = $options['discount_type'];
        $builder
            ->add(self::CART_CONDITIONS, CartConditionsType::class, [
                'label' => $this->trans('Cart conditions', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
            ])
        ;

        if (in_array($discountType, [DiscountType::FREE_SHIPPING, DiscountType::ORDER_LEVEL, DiscountType::FREE_GIFT])) {
            $builder->add(self::DELIVERY_CONDITIONS, DeliveryConditionsType::class, [
                'label' => $this->trans('On delivery', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/conditions_form_theme.html.twig',
            // Override the ToggleChildrenChoiceType choice type
            'choice_type' => EnrichedChoiceType::class,
            'choice_options' => [
                'flex_direction' => 'row',
                'choice_attr' => [
                    $this->trans('Cart conditions', 'Admin.Catalog.Feature') => [
                        'help' => $this->trans('Based on specific product(s) or product segment', 'Admin.Catalog.Feature'),
                    ],
                    $this->trans('On delivery', 'Admin.Catalog.Feature') => [
                        'help' => $this->trans('Based on specific carrier(s) or product segment', 'Admin.Catalog.Feature'),
                    ],
                ],
                'placeholder' => $this->trans('None', 'Admin.Catalog.Feature'),
                'placeholder_attr' => [
                    'help' => $this->trans('No condition applied.', 'Admin.Catalog.Feature'),
                ],
            ],
        ]);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
