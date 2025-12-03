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
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountConditionsType extends TranslatorAwareType
{
    public const PRODUCT_CONDITIONS = 'product';
    public const CART_CONDITIONS = 'cart';
    public const DELIVERY_CONDITIONS = 'delivery';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountType = $options['discount_type'];
        $builder
            ->add(self::PRODUCT_CONDITIONS, ProductConditionsType::class, [
                'label' => $this->trans('Product conditions', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('Require a single product or product segment in the cart for the discount to become active.', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'choice_options' => [
                    'label' => false,
                ],
            ])
            ->add(self::CART_CONDITIONS, CartConditionsType::class, [
                'label' => $this->trans('Cart conditions', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('Set a minimum purchase amount or product quantity for the discount to become active.', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'choice_options' => [
                    'label' => false,
                ],
            ])
        ;

        if (in_array($discountType, [DiscountType::FREE_SHIPPING, DiscountType::ORDER_LEVEL, DiscountType::FREE_GIFT])) {
            $builder->add(self::DELIVERY_CONDITIONS, DeliveryConditionsType::class, [
                'label' => $this->trans('Delivery conditions', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('Limit the discount\'s availability based on the customer\'s country or chosen delivery method.', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'choice_options' => [
                    'label' => false,
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
        ]);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
