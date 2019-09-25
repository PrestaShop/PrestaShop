<?php
/**
 * 2007-2019 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class FeatureGridDefinitionFactory defines features grid structure.
 */
final class FeatureGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'feature';

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
        return $this->trans('Features', [], 'Admin.Catalog.Feature');
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
                        'bulk_field' => 'id_feature',
                    ])
            )
            ->add(
                (new DataColumn('id_feature'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_feature',
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
                (new DataColumn('values_count'))
                    ->setName($this->trans('Values', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'values_count',
                    ])
            )
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_feature',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'admin_features_update_position',
                        'record_route_params' => [
                            'id_feature' => 'id_feature',
                        ],
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        //@todo uncomment when form is migrated
//                        ->add(
//                            (new LinkRowAction('view'))
//                                ->setName($this->trans('View', [], 'Admin.Actions'))
//                                ->setIcon('zoom_in')
//                                ->setOptions([
//                                    'route' => 'admin_features_view',
//                                    'route_param_name' => 'featureId',
//                                    'route_param_field' => 'id_feature',
//                                ])
//                        )
//                        ->add(
//                            (new LinkRowAction('edit'))
//                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
//                                ->setIcon('edit')
//                                ->setOptions([
//                                    'route' => 'admin_features_edit',
//                                    'route_param_name' => 'featureId',
//                                    'route_param_field' => 'id_feature',
//                                ])
//                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'POST',
                                'route' => 'admin_features_delete',
                                'route_param_name' => 'featureId',
                                'route_param_field' => 'id_feature',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            );

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_feature', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_feature')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('values_count', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search values', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('values_count')
            )
            ->add(
                (new Filter('position', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search position', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('position')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search',
                        'reset_route_params' => [
                            'controller' => 'feature',
                            'action' => 'index',
                        ],
                        'redirect_route' => 'admin_features_index',
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_features_bulk_delete',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                ])
            );
    }
}
