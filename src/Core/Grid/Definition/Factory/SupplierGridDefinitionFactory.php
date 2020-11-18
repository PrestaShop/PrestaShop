<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SupplierGridDefinitionFactory creates definition for supplier grid.
 */
final class SupplierGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'supplier';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Suppliers', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_supplier',
                ])
            )
            ->add((new DataColumn('id_supplier'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_supplier',
                ])
            )
            ->add((new ImageColumn('logo'))
                ->setName($this->trans('Logo', [], 'Admin.Global'))
                ->setOptions([
                    'src_field' => 'logo',
                ])
            )
            ->add((new LinkColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                    'route' => 'admin_suppliers_edit',
                    'route_param_name' => 'supplierId',
                    'route_param_field' => 'id_supplier',
                ])
            )
            ->add((new DataColumn('products_count'))
                ->setName($this->trans('Number of products', [], 'Admin.Catalog.Feature'))
                ->setOptions([
                    'field' => 'products_count',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_supplier',
                    'route' => 'admin_suppliers_toggle_status',
                    'route_param_name' => 'supplierId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Actions'))
                            ->setIcon('zoom_in')
                            ->setOptions([
                                'route' => 'admin_suppliers_view',
                                'route_param_name' => 'supplierId',
                                'route_param_field' => 'id_supplier',
                                'clickable_row' => true,
                            ])
                        )
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_suppliers_edit',
                                'route_param_name' => 'supplierId',
                                'route_param_field' => 'id_supplier',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_suppliers_delete',
                                'route_param_name' => 'supplierId',
                                'route_param_field' => 'id_supplier',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_supplier', TextType::class))
                ->setAssociatedColumn('id_supplier')
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                    ],
                    'required' => false,
                ])
            )
            ->add((new Filter('name', TextType::class))
                ->setAssociatedColumn('name')
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                    ],
                    'required' => false,
                ])
            )
            ->add((new Filter('products_count', TextType::class))
                ->setAssociatedColumn('products_count')
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Number of products', [], 'Admin.Catalog.Feature'),
                    ],
                    'required' => false,
                ])
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
                ->setTypeOptions([
                    'required' => false,
                ])
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search',
                    'reset_route_params' => [
                        'controller' => 'supplier',
                        'action' => 'index',
                    ],
                    'redirect_route' => 'admin_suppliers_index',
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('suppliers_enable_selection'))
                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_suppliers_bulk_enable',
                ])
            )
            ->add((new SubmitBulkAction('suppliers_disable_selection'))
                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_suppliers_bulk_disable',
                ])
            )
            ->add((new SubmitBulkAction('suppliers_delete'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_suppliers_bulk_delete',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add((new LinkGridAction('import'))
                ->setName($this->trans('Import', [], 'Admin.Actions'))
                ->setIcon('cloud_upload')
                ->setOptions([
                    'route' => 'admin_import',
                    'route_params' => [
                        'import_type' => 'suppliers',
                    ],
                ])
            )
            ->add((new LinkGridAction('export'))
                ->setName($this->trans('Export', [], 'Admin.Actions'))
                ->setIcon('cloud_download')
                ->setOptions([
                    'route' => 'admin_suppliers_export',
                ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }
}
