<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\ImageType\DeleteImageTypeRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PixelDataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\StatusColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CustomerGridDefinitionFactory defines customers grid structure.
 */
final class ImageTypeGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;

    public const GRID_ID = 'image_type';

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
        $columns = (new ColumnCollection())
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
                (new PixelDataColumn('width'))
                    ->setName($this->trans('Width', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'width',
                    ])
            )
            ->add(
                (new PixelDataColumn('height'))
                    ->setName($this->trans('Height', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'height',
                    ])
            )
            ->add(
                (new StatusColumn('products'))
                    ->setName($this->trans('Products', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'products',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new StatusColumn('categories'))
                    ->setName($this->trans('Categories', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'categories',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new StatusColumn('manufacturers'))
                    ->setName($this->trans('Brands', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'manufacturers',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new StatusColumn('suppliers'))
                    ->setName($this->trans('Suppliers', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'suppliers',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new StatusColumn('stores'))
                    ->setName($this->trans('Stores', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'stores',
                        'alignment' => 'center',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            )
        ;

        return $columns;
    }

    /**
     * @return RowActionCollection
     */
    private function getRowActions(): RowActionCollection
    {
        $rowActionCollection = new RowActionCollection();
        $rowActionCollection
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_image_settings_edit',
                        'route_param_name' => 'imageTypeId',
                        'route_param_field' => 'id_image_type',
                        'clickable_row' => true,
                    ])
            )
            ->add(
                (new DeleteImageTypeRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => 'admin_image_settings_delete',
                        'id_field' => 'id_image_type',
                    ])
            );

        return $rowActionCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_image_type', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_image_type')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search Name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('width', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search Width', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('width')
            )
            ->add(
                (new Filter('height', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search Height', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('height')
            )
            ->add(
                (new Filter('products', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('products')
            )
            ->add(
                (new Filter('categories', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('categories')
            )
            ->add(
                (new Filter('manufacturers', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('manufacturers')
            )
            ->add(
                (new Filter('suppliers', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('suppliers')
            )
            ->add(
                (new Filter('stores', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('stores')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_image_settings_index',
                    ])
                    ->setAssociatedColumn('actions')
            );

        return $filters;
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

    /*
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_image_settings_bulk_delete')
            );
    }
}
