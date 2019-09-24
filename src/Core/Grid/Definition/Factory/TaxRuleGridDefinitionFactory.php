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
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\TaxRule\DeleteTaxRulesBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\TaxRule\EditTaxRuleRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Defines tax rule grid for tax rules group edit page
 */
final class TaxRuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'tax_rule_grid';

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
        return $this->trans('Taxes', [], 'Admin.Global');
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
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('state'))
                    ->setName($this->trans('State', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'state_name',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('zipcode'))
                    ->setName($this->trans('Zip/Postal code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'zipcode',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('behavior'))
                    ->setName($this->trans('Behavior', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'behavior',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('tax'))
                    ->setName($this->trans('Tax', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'rate',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('description'))
                    ->setName($this->trans('Description', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'description',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new EditTaxRuleRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'edit_route' => 'admin_tax_rules_edit',
                                        'load_route' => 'admin_tax_rules_load',
                                        'route_param_name' => 'taxRuleId',
                                        'route_param_field' => 'id_tax_rule',
                                    ])
                            )
                            ->add(
                                (new SubmitRowAction('delete'))
                                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                    ->setIcon('delete')
                                    ->setOptions([
                                        'confirm_message' => $this->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                        'route' => 'admin_tax_rules_delete',
                                        'route_param_name' => 'taxRuleId',
                                        'route_param_field' => 'id_tax_rule',
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new DeleteTaxRulesBulkAction('delete_selection'))
                    ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                    ->setOptions([
                        'tax_rules_bulk_delete_route' => 'admin_tax_rules_bulk_delete',
                        'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                    ])
            );
    }
}
