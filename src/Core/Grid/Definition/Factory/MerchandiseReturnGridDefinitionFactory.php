<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Class MerchandiseReturnGridDefinitionFactory builds grid definition for merchandise returns grid.
 */
final class MerchandiseReturnGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'merchandise_return';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Merchandise Returns', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_order_return'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_order_return',
                    ])
            )
            ->add(
                (new DataColumn('id_order'))
                    ->setName($this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'id_order',
                    ])
            )
            ->add(
                (new BadgeColumn('status'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'status',
                    ])
            )
            ->add(
                (new DataColumn('date_add'))
                    ->setName($this->trans('Date issued', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'date_add',
                    ])
            )
        ;

        return $columns;
    }
}
