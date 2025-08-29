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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\ReorderPositionsButtonType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines carriers grid
 */
class CarrierGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;
    use BulkDeleteActionTrait;

    public const GRID_ID = 'carrier';

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var bool
     */
    protected $showExternalModuleColumn;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $dbPrefix,
        Connection $connection
    ) {
        parent::__construct($hookDispatcher);
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;

        $this->showExternalModuleColumn = $this->hasActiveExternalModuleCarriers();
    }

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
        return $this->trans('Carriers', [], 'Admin.Shipping.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = new ColumnCollection();

        $columns
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_carrier',
                    ])
            )
            ->add(
                (new DataColumn('id_carrier'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_carrier',
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
                (new ImageColumn('logo'))
                    ->setName($this->trans('Logo', [], 'Admin.Global'))
                    ->setOptions([
                        'src_field' => 'logo',
                    ])
            )
            ->add(
                (new DataColumn('delay'))
                    ->setName($this->trans('Delay', [], 'Admin.Shipping.Feature'))
                    ->setOptions([
                        'field' => 'delay',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_carrier',
                        'route' => 'admin_carriers_toggle_status',
                        'route_param_name' => 'carrierId',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ToggleColumn('is_free'))
                    ->setName($this->trans('Free shipping', [], 'Admin.Shipping.Feature'))
                    ->setOptions([
                        'field' => 'is_free',
                        'primary_field' => 'id_carrier',
                        'route' => 'admin_carriers_toggle_is_free',
                        'route_param_name' => 'carrierId',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_carrier',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'admin_carriers_update_position',
                    ])
            );

        if ($this->showExternalModuleColumn) {
            $columns
                ->add(
                    (new DataColumn('external_module_name'))
                        ->setName($this->trans('Module name', [], 'Admin.Global'))
                        ->setOptions([
                            'field' => 'external_module_name',
                        ])
                );
        }

        $columns
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            );

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = new FilterCollection();

        $filters
            ->add(
                (new Filter('id_carrier', TextType::class))
                    ->setAssociatedColumn('id_carrier')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setAssociatedColumn('name')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('delay', TextType::class))
                    ->setAssociatedColumn('delay')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('is_free', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('is_free')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('position', ReorderPositionsButtonType::class))
                    ->setAssociatedColumn('position')
            );

        if ($this->showExternalModuleColumn) {
            $filters
                ->add(
                    (new Filter('external_module_name', TextType::class))
                        ->setAssociatedColumn('external_module_name')
                        ->setTypeOptions([
                            'required' => false,
                        ])
                );
        }

        $filters
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_carriers_index',
                    ])
            );

        return $filters;
    }

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

    private function getRowActions(): RowActionCollectionInterface
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_carriers_edit',
                        'route_param_name' => 'carrierId',
                        'route_param_field' => 'id_carrier',
                        'clickable_row' => true,
                    ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_carriers_delete',
                    'carrierId',
                    'id_carrier'
                )
            );
    }

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_carriers_bulk_enable_status',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_carriers_bulk_disable_status',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_carriers_bulk_delete')
            );
    }

    protected function hasActiveExternalModuleCarriers()
    {
        $sql = 'SELECT count(external_module_name)
                FROM ' . $this->dbPrefix . 'carrier
                WHERE deleted = 0 AND external_module_name != ""';

        return (int) $this->connection->fetchOne($sql) > 0;
    }
}
