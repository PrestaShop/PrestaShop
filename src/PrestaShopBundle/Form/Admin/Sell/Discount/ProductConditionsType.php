<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DiscountProductSegment;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\When;

class ProductConditionsType extends TranslatorAwareType
{
    public const NONE = 'none';

    public const CHEAPEST_PRODUCT = 'cheapest_product';
    public const SPECIFIC_PRODUCTS = 'specific_products';
    public const PRODUCT_SEGMENT = 'product_segment';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $discountType = $options['discount_type'];

        $builder->add(self::NONE, HiddenType::class, [
            'label' => $this->trans('None', 'Admin.Catalog.Feature'),
        ]);

        if ($discountType === DiscountType::PRODUCT_LEVEL) {
            /*
             * The cheapest product target is no longer offered when building a discount, and that is
             * deliberate. It is still offered here to a discount that ALREADY targets it, because the
             * cart rule keeps storing it and the price engine keeps applying it: such a discount exists
             * on any shop that used the legacy voucher form, or that created one before the option was
             * taken out. Without the field the selector falls back to "none", which is not a target a
             * product level discount may have, so the merchant is shown the wrong target and then cannot
             * save the discount at all: the save is refused with "Product discount must target at least
             * one product", naming something the form never offered.
             *
             * The listener runs ahead of the one ToggleChildrenChoiceType registers, which builds the
             * radio choices from the children present at that moment.
             */
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();
                if (is_array($data) && ($data['children_selector'] ?? null) === self::CHEAPEST_PRODUCT) {
                    $event->getForm()->add(self::CHEAPEST_PRODUCT, HiddenType::class, [
                        'label' => $this->trans('Cheapest product', 'Admin.Catalog.Feature'),
                    ]);
                }
            }, 10);

            $specificProductsLabel = $this->trans('Single product', 'Admin.Catalog.Feature');
            $specificProductsLimit = 1;
        } else {
            $specificProductsLabel = $this->trans('Specific products', 'Admin.Catalog.Feature');
            $specificProductsLimit = 0;
        }

        $builder
            ->add(self::SPECIFIC_PRODUCTS, ProductSearchType::class, [
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'entry_type' => SpecificProductType::class,
                'limit' => $specificProductsLimit,
                'label' => $specificProductsLabel,
                'include_combinations' => false,
                'required' => false,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::SPECIFIC_PRODUCTS
                        ),
                        constraints: [
                            new Count(
                                min: 1,
                                minMessage: $this->trans('You need to select at least one product.', 'Admin.Catalog.Notification'),
                            ),
                        ],
                    ),
                ],
            ])
            ->add(self::PRODUCT_SEGMENT, DiscountProductSegmentType::class, [
                'label' => $this->trans('Product segment', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::PRODUCT_SEGMENT
                        ),
                        constraints: [
                            new DiscountProductSegment(),
                        ]
                    ),
                ],
            ])
        ;
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
        return ToggleChildrenChoiceType::class;
    }
}
