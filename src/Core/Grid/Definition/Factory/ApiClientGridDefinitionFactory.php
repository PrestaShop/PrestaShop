<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;

/**
 * Class ApiClientGridDefinitionFactory is responsible for creating new instance of Api Client grid definition.
 */
final class ApiClientGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'api_client';

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Api Clients', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        return (new ColumnCollection())
            ->add(
                (new IdentifierColumn('id_api_client'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'identifier_field' => 'id_api_client',
                        'clickable' => false,
                    ])
            )
            ->add(
                (new DataColumn('client_name'))
                    ->setName($this->trans('Client Name', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'client_name',
                    ])
            )
            ->add(
                (new DataColumn('client_id'))
                    ->setName($this->trans('Client ID', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'client_id',
                    ])
            )
            ->add(
                (new DataColumn('external_issuer'))
                    ->setName($this->trans('External issuer', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'external_issuer',
                    ])
            )
            ->add(
                (new ToggleColumn('enabled'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_api_client',
                        'route' => 'admin_api_clients_toggle_active',
                        'route_param_name' => 'apiClientId',
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => $this->getRowActions(),
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowActions(): RowActionCollection
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('edit'))
                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                ->setIcon('edit')
                ->setOptions([
                    'route' => 'admin_api_clients_edit',
                    'route_param_name' => 'apiClientId',
                    'route_param_field' => 'id_api_client',
                ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_api_clients_delete',
                    'apiClientId',
                    'id_api_client',
                )
            );

        return $rowActions;
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
