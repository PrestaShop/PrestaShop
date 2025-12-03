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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotCustomizableProduct;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType as DiscountTypeVo;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * This is the form root element for discount form.
 */
class DiscountType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $discountType = $options['discount_type'];
        $builder
            ->add('information', DiscountInformationType::class, [
                'discount_type' => $discountType,
            ])
            ->add('period', DiscountPeriodType::class)
            ->add('customer_eligibility', DiscountCustomerEligibilityType::class)
            ->add('conditions', DiscountConditionsType::class, [
                'label' => $this->trans('Select discount conditions', 'Admin.Catalog.Feature'),
                'discount_type' => $discountType,
            ])
        ;

        if ($discountType === DiscountTypeVo::CART_LEVEL || $discountType === DiscountTypeVo::ORDER_LEVEL || $discountType === DiscountTypeVo::PRODUCT_LEVEL) {
            $labelSubtitle = match ($discountType) {
                DiscountTypeVo::CART_LEVEL => $this->trans('This discount applies on cart.', 'Admin.Catalog.Feature'),
                DiscountTypeVo::ORDER_LEVEL => $this->trans('This discount applies on order.', 'Admin.Catalog.Feature'),
                DiscountTypeVo::PRODUCT_LEVEL => $this->trans('This discount applies on catalog products.', 'Admin.Catalog.Feature'),
            };

            $builder
                ->add('value', DiscountValueType::class, [
                    'label' => $this->trans('Choose a discount value', 'Admin.Catalog.Feature'),
                    'label_subtitle' => $labelSubtitle,
                ])
            ;
        }

        if ($discountType === DiscountTypeVo::FREE_GIFT) {
            $builder
                ->add('free_gift', ProductSearchType::class, [
                    'layout' => EntitySearchInputType::LIST_LAYOUT,
                    'label' => $this->trans('Free gift', 'Admin.Catalog.Feature'),
                    'label_help_box' => $this->trans('You can choose a free gift.', 'Admin.Catalog.Help'),
                    'include_combinations' => true,
                    'empty_state' => $this->trans('No product selected', 'Admin.Catalog.Feature'),
                    'identifier_field' => 'gift_product',
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                        new NotCustomizableProduct(['message' => $this->trans('Product with required customization fields cannot be used as a gift.', 'Admin.Catalog.Notification')]),
                    ],
                ])
            ;
        }

        $builder
            ->add('usability', DiscountUsabilityType::class, [
                'label' => $this->trans('Usability conditions', 'Admin.Catalog.Feature'),
                'available_cart_rule_types' => $options['available_cart_rule_types'] ?? [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            'available_cart_rule_types' => [],
        ]);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
        $resolver->setAllowedTypes('available_cart_rule_types', ['array']);
    }
}
