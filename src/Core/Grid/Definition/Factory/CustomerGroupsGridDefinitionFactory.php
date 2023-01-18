<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

class CustomerGroupsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'customer_groups';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Customer Groups', [], 'Admin.Navigation.Menu');
    }

    protected function getColumns()
    {

        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('title_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('id_group'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('reduction'))
                    ->setName($this->trans('Reduction', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'reduction',
                    ])
            )
            ->add(
                (new DataColumn('price_display_method'))
                    ->setName($this->trans('Price display Method', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'price_display_method',
                    ])
            )
            ->add(
                (new DataColumn('show_prices'))
                    ->setName($this->trans('Show prices', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'show_prices',
                    ])
            )
        ;
    }
}
