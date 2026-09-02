<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\ReorderPositionsButtonType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines attribute groups grid
 */
final class AttributeGroupGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'attribute_group';

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
        return $this->trans('Attributes', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_attribute_group',
                ])
            )
            ->add((new DataColumn('id_attribute_group'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_attribute_group',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('values'))
                ->setName($this->trans('Values', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'values',
                    'sortable' => false,
                ])
            )
            ->add((new PositionColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'id_field' => 'id_attribute_group',
                    'position_field' => 'position',
                    'update_method' => 'POST',
                    'update_route' => 'admin_attribute_groups_update_position',
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
                                'route' => 'admin_attributes_index',
                                'route_param_name' => 'attributeGroupId',
                                'route_param_field' => 'id_attribute_group',
                                'clickable_row' => true,
                            ])
                        )
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_attribute_groups_edit',
                                'route_param_name' => 'attributeGroupId',
                                'route_param_field' => 'id_attribute_group',
                            ])
                        )
                        ->add(
                            $this->buildDeleteAction(
                                'admin_attribute_groups_delete',
                                'attributeGroupId',
                                'id_attribute_group',
                                Request::METHOD_DELETE
                            )
                        ),
                ])
            );
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
                        'import_type' => 'attribute_groups',
                    ],
                ])
            )
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', [], 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'admin_attribute_groups_export',
                    ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_attribute_group', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_attribute_group')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('position', ReorderPositionsButtonType::class))
                ->setAssociatedColumn('position')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'admin_attribute_groups_index',
                ])
                ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_attribute_groups_bulk_delete')
            );
    }
}
