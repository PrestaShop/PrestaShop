<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductSupplierCollectionType extends CollectionType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->translator->trans('Supplier reference(s)', [], 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h4',
            'entry_type' => ProductSupplierType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__PRODUCT_SUPPLIER_INDEX__',
            'attr' => [
                'class' => 'product-suppliers-collection',
            ],
            'alert_message' => $this->translator->trans(
                'You can specify product reference(s) for each associated supplier. Click "%save_label%" after changing selected suppliers to display the associated product references.',
                [
                    '%save_label%' => $this->translator->trans('Save', [], 'Admin.Actions'),
                ],
                'Admin.Catalog.Help'
            ),
            'alert_position' => 'prepend',
            'block_prefix' => 'product_supplier_collection',
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product_suppliers.html.twig',
        ]);
    }
}
