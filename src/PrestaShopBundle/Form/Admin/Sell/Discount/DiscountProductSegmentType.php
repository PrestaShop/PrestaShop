<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Sell\Product\Description\ManufacturerType;
use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\GroupedItemCollectionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\When;

class DiscountProductSegmentType extends TranslatorAwareType
{
    public const CATEGORY = 'category';
    public const MANUFACTURER = 'manufacturer';
    public const FEATURES = 'features';

    public const SUPPLIER = 'supplier';
    public const ATTRIBUTES = 'attributes';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::MANUFACTURER, ManufacturerType::class, [
                'label' => $this->trans('Brand', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add(self::CATEGORY, CategoryChoiceTreeType::class, [
                'label' => $this->trans('Category', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
            ])
            ->add(self::SUPPLIER, DiscountSupplierType::class, [
                'label' => $this->trans('Supplier', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add(self::ATTRIBUTES, GroupedItemCollectionType::class, [
                'label' => $this->trans('Attributes', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'select_button_label' => $this->trans('Select attributes', 'Admin.Catalog.Feature'),
                'modal_search_placeholder' => $this->trans('Search for attribute...', 'Admin.Catalog.Feature'),
                'modal_title' => $this->trans('Select attributes', 'Admin.Catalog.Feature'),
                'modal_select_label' => $this->trans('Select {selectedItemsNb} attribute(s)', 'Admin.Catalog.Feature'),
                'modal_loading' => $this->trans('Loading attributes', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add(self::FEATURES, GroupedItemCollectionType::class, [
                'label' => $this->trans('Features', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'select_button_label' => $this->trans('Select features', 'Admin.Catalog.Feature'),
                'modal_search_placeholder' => $this->trans('Search for feature...', 'Admin.Catalog.Feature'),
                'modal_title' => $this->trans('Select features', 'Admin.Catalog.Feature'),
                'modal_select_label' => $this->trans('Select {selectedItemsNb} feature(s)', 'Admin.Catalog.Feature'),
                'modal_loading' => $this->trans('Loading features', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => $this->trans('Minimum product quantity', 'Admin.Catalog.Feature'),
                'attr' => [
                    'class' => 'js-comma-transformer',
                ],
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().getParent().get("children_selector").getData() === "%s"',
                            ProductConditionsType::PRODUCT_SEGMENT,
                        ),
                        constraints: [
                            new NotBlank(),
                            new Type([
                                'type' => 'numeric',
                            ]),
                            new GreaterThanOrEqual([
                                'value' => 1,
                            ]),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'error_bubbling' => false,
        ]);
    }
}
