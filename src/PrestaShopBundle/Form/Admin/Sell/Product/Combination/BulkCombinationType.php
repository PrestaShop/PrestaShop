<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\AccordionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * For combination update in bulk action
 */
class BulkCombinationType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', BulkCombinationStockType::class)
            ->add('price', BulkCombinationPriceType::class, [
                'product_id' => $options['product_id'],
                'country_id' => $options['country_id'],
                'shop_id' => $options['shop_id'],
            ])
            ->add('references', BulkCombinationReferencesType::class)
            ->add('images', BulkCombinationImagesType::class, [
                'label' => $this->trans('Images', 'Admin.Global'),
                'product_id' => $options['product_id'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'label_subtitle' => $this->trans('You can bulk edit the selected combinations by enabling and filling each field that needs to be updated.', 'Admin.Catalog.Feature'),
                'expand_first' => false,
                'display_one' => false,
                'required' => false,
                'attr' => [
                    'class' => 'bulk-combination-form',
                ],
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
            ])
            ->setRequired([
                'product_id',
                'country_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('country_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }

    public function getParent()
    {
        return AccordionType::class;
    }
}
