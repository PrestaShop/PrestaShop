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
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateDiscountType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountTypes = [
            DiscountType::CART_LEVEL => [
                'type' => DiscountType::CART_LEVEL,
                'label' => $this->trans('On cart amount', 'Admin.Catalog.Feature'),
                'icon' => 'shopping_cart',
                'help' => $this->trans('Apply on total cart', 'Admin.Catalog.Feature'),
            ],
            DiscountType::PRODUCT_LEVEL => [
                'type' => DiscountType::PRODUCT_LEVEL,
                'label' => $this->trans('On catalog products', 'Admin.Catalog.Feature'),
                'icon' => 'shoppingmode',
                'help' => $this->trans('Apply on catalog products', 'Admin.Catalog.Feature'),
            ],
            DiscountType::FREE_GIFT => [
                'type' => DiscountType::FREE_GIFT,
                'label' => $this->trans('Free gift', 'Admin.Catalog.Feature'),
                'icon' => 'card_giftcard',
                'help' => $this->trans('Apply on free gift', 'Admin.Catalog.Feature'),
            ],
            DiscountType::FREE_SHIPPING => [
                'type' => DiscountType::FREE_SHIPPING,
                'label' => $this->trans('On free shipping', 'Admin.Catalog.Feature'),
                'icon' => 'local_shipping',
                'help' => $this->trans('Apply on shipping fees', 'Admin.Catalog.Feature'),
            ],
            DiscountType::ORDER_LEVEL => [
                'type' => DiscountType::ORDER_LEVEL,
                'label' => $this->trans('On total order', 'Admin.Catalog.Feature'),
                'icon' => 'article',
                'help' => $this->trans('Apply on cart and shipping fees', 'Admin.Catalog.Feature'),
            ],
        ];

        // Transform associative array into object to pass more data for custom display (help, icon, ...)
        $transformedData = array_map(function (array $discountType) {
            return json_decode(json_encode($discountType), false);
        }, $discountTypes);

        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('This type cannot be modified after being saved.', 'Admin.Catalog.Feature'),
                'choices' => $transformedData,
                'choice_value' => 'type',
                'choice_label' => 'label',
                'choice_name' => 'type',
                'expanded' => true,
                'multiple' => false,
                'data' => $transformedData[DiscountType::CART_LEVEL],
                'block_prefix' => 'discount_type',
                'attr' => [
                    'class' => 'discount-type',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/create_form_theme.html.twig',
        ]);
    }
}
