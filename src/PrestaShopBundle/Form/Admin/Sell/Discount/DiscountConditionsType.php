<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                'discount_type' => $discountType,
            ])
        ;

        // Product level has no cart conditions
        if ($discountType !== DiscountType::PRODUCT_LEVEL) {
            $builder->add(self::CART_CONDITIONS, CartConditionsType::class, [
                'label' => $this->trans('Cart conditions', 'Admin.Catalog.Feature'),
                'label_subtitle' => $this->trans('Set a minimum purchase amount or product quantity for the discount to become active.', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'choice_options' => [
                    'label' => false,
                ],
            ]);
        }

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
