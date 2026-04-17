<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Sell\Product\Details\ReferencesType;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\ProductSupplierCollectionType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form to edit Combination details.
 */
class CombinationFormType extends TranslatorAwareType
{
    /**
     * @var EventSubscriberInterface
     */
    private $combinationListener;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param EventSubscriberInterface $combinationListener
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        EventSubscriberInterface $combinationListener
    ) {
        parent::__construct($translator, $locales);
        $this->combinationListener = $combinationListener;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cover_thumbnail_url', ImagePreviewType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'combination-cover-row',
                ],
            ])
            ->add('header', CombinationHeaderType::class)
            ->add('stock', CombinationStockType::class, [
                'product_id' => $options['product_id'],
            ])
            ->add('price_impact', CombinationPriceImpactType::class)
            ->add('references', ReferencesType::class, [
                'columns_number' => 3,
            ])
            ->add('default_supplier_id', HiddenType::class)
            ->add('product_suppliers', ProductSupplierCollectionType::class, [
                'alert_message' => $this->trans('This interface allows you to specify the suppliers of the current combination.', 'Admin.Catalog.Help'),
            ])
            ->add('images', CombinationImagesChoiceType::class, [
                'product_id' => $options['product_id'],
                'label_tag_name' => 'h3',
            ])
        ;

        /*
         * This listener adapts the content of the form based on the data, it can remove add or transforms some
         * of the internal fields @see CombinationListener
         */
        $builder->addEventSubscriber($this->combinationListener);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setDefaults([
                'required' => false,
                'label' => false,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
                'use_default_themes' => false,
            ])
        ;
    }
}
