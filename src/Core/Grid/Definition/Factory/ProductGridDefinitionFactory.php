<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Defines products grid name, its columns, actions, bulk actions and filters.
 */
final class ProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var bool
     */
    private $isStockManagementEnabled;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $isStockManagementEnabled
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $isStockManagementEnabled
    ) {
        parent::__construct($hookDispatcher);
        $this->isStockManagementEnabled = $isStockManagementEnabled;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Products', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_product',
                    ])
            )
            ->add(
                (new DataColumn('id_product'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_product',
                    ])
            )
            ->add(
                (new ImageColumn('image'))
                    ->setName($this->trans('Image', [], 'Admin.Global'))
                    ->setOptions([
                        'src_field' => 'image',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('reference'))
                    ->setName($this->trans('Reference', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'reference',
                    ])
            )
            ->add(
                (new DataColumn('category'))
                    ->setName($this->trans('Category', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'category',
                    ])
            )
            ->add(
                (new DataColumn('price_tax_excluded'))
                    ->setName($this->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'price_tax_excluded',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_product',
                        'route' => 'admin_products_toggle_status',
                        'route_param_name' => 'productId',
                    ])
            )
        ;

        //todo: test on or off
        if ($this->isStockManagementEnabled) {
            $columns->addAfter(
                'price_tax_excluded',
                (new DataColumn('quantity'))
                    ->setName($this->trans('Quantity', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'quantity',
                    ])
            );
        }

        return $columns;

//        todo: position when category filter is used
        // @see https://github.com/sarjon/PrestaShop/blob/42a80d5931b50e641b8030b82845cec1a3bb5118/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/Lists/products_table.html.twig#L72
    }
}
