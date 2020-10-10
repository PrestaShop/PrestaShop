<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Creates definition for image types grid.
 */
class ImageTypeGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'image_type';

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
        return $this->trans('Image Settings', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_image_type',
                    ])
            )
            ->add(
                (new DataColumn('id_image_type'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_image_type',
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
                (new DataColumn('width'))
                    ->setName($this->trans('Width', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'width',
                    ])
            )
            ->add(
                (new DataColumn('height'))
                    ->setName($this->trans('Height', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'height',
                    ])
            )
            ->add(
                (new BooleanColumn('products'))
                    ->setName($this->trans('Products', [], 'Admin.Global'))
                    ->setOptions([
                        'use_icon' => true,
                        'field' => 'products',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new BooleanColumn('categories'))
                    ->setName($this->trans('Categories', [], 'Admin.Global'))
                    ->setOptions([
                        'use_icon' => true,
                        'field' => 'categories',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new BooleanColumn('manufacturers'))
                    ->setName($this->trans('Brands', [], 'Admin.Global'))
                    ->setOptions([
                        'use_icon' => true,
                        'field' => 'manufacturers',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new BooleanColumn('suppliers'))
                    ->setName($this->trans('Suppliers', [], 'Admin.Global'))
                    ->setOptions([
                        'use_icon' => true,
                        'field' => 'suppliers',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new BooleanColumn('stores'))
                    ->setName($this->trans('Stores', [], 'Admin.Global'))
                    ->setOptions([
                        'use_icon' => true,
                        'field' => 'stores',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_image_type', TextType::class))
                    ->setAssociatedColumn('id_image_type')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setAssociatedColumn('name')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('width', TextType::class))
                    ->setAssociatedColumn('width')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('height', TextType::class))
                    ->setAssociatedColumn('height')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('products', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('products')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('categories', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('categories')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('manufacturers', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('manufacturers')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('suppliers', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('suppliers')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('stores', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('stores')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_image_settings_index',
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }
}
