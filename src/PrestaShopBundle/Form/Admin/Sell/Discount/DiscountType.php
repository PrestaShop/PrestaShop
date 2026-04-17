<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                'available_discount_types' => $options['available_discount_types'] ?? [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            'available_discount_types' => [],
        ]);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
        $resolver->setAllowedTypes('available_discount_types', ['array']);
    }
}
