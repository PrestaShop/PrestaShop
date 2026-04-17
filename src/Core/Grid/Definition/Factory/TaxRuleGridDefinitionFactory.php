<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;

/**
 * Defines tax rules grid
 */
class TaxRuleGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'tax_rules';

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
        return $this->trans('Tax rules', [], 'Admin.Navigation.Menu');
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
                        'bulk_field' => 'id_tax_rule',
                    ])
            )
            ->add(
                (new DataColumn('country'))
                    ->setName($this->trans('Country', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'country_name',
                    ])
            )
            ->add(
                (new DataColumn('state'))
                    ->setName($this->trans('State', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'state_name',
                    ])
            )
            ->add(
                (new DataColumn('zipcode'))
                    ->setName($this->trans('Zip/Postal code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'zipcode',
                    ])
            )
            ->add(
                (new DataColumn('behavior'))
                    ->setName($this->trans('Behavior', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'behavior',
                    ])
            )
            ->add(
                (new DataColumn('rate'))
                    ->setName($this->trans('Tax', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'rate',
                    ])
            )
            ->add(
                (new DataColumn('description'))
                    ->setName($this->trans('Description', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'description',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                        /*
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_tax_rule_edit',
                                        'route_param_name' => 'taxRuleId',
                                        'route_param_field' => 'id_tax_rule',
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_tax_rule_delete',
                                    'taxRuleId',
                                    'id_tax_rule',
                                    Request::METHOD_DELETE
                                )
                            )*/,
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection()
            /*
            ->add(
                $this->buildBulkDeleteAction('admin_tax_rules_bulk_delete')
            )
            */;
    }
}
