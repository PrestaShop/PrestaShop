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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class responsible for providing columns, filters, actions for catalog price rule list.
 */
final class CatalogPriceRuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'catalog_price_rule';

    public function __construct(
        HookDispatcherInterface $hookDispatcher
    ) {
        parent::__construct($hookDispatcher);
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
        return $this->trans('Catalog price rules', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
            ->setOptions([
                'bulk_field' => 'id_specific_price_rule',
            ])
            )
            ->add((new DataColumn('id_specific_price_rule'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_specific_price_rule',
            ])
            )
            ->add((new DataColumn('name'))
            ->setName($this->trans('Name', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'name',
            ])
            )
            ->add((new DataColumn('shop'))
            ->setName($this->trans('Store', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'shop',
            ])
            )
            ->add((new DataColumn('currency'))
            ->setName($this->trans('Currency', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'currency',
            ])
            )
            ->add((new DataColumn('country'))
            ->setName($this->trans('Country', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'country',
            ])
            )
            ->add((new DataColumn('group_name'))
            ->setName($this->trans('Group', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'group_name',
            ])
            )
            ->add((new DataColumn('from_quantity'))
            ->setName($this->trans('From quantity', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'field' => 'from_quantity',
            ])
            )
            ->add((new DataColumn('reduction_type'))
            ->setName($this->trans('Reduction type', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'field' => 'reduction_type',
            ])
            )
            ->add((new DataColumn('reduction'))
            ->setName($this->trans('Reduction', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'field' => 'reduction',
            ])
            )
            ->add((new DateTimeColumn('date_from'))
            ->setName($this->trans('Beginning', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'format' => 'Y‑m‑d H:i', // note the use of non-breaking hyphens (U+2011)
                'field' => 'date_from',
            ])
            )
            ->add((new DateTimeColumn('date_to'))
            ->setName($this->trans('End', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'format' => 'Y-m-d H:i',
                'field' => 'date_to',
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => (new RowActionCollection())
                    ->add((new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_catalog_price_rules_edit',
                        'route_param_name' => 'catalogPriceRuleId',
                        'route_param_field' => 'id_specific_price_rule',
                    ])
                    )
                    ->add((new SubmitRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'method' => 'POST',
                        'route' => 'admin_catalog_price_rules_delete',
                        'route_param_name' => 'catalogPriceRuleId',
                        'route_param_field' => 'id_specific_price_rule',
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
            ->add((new Filter('id_specific_price_rule', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('id_specific_price_rule')
            )
            ->add((new Filter('name', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('name')
            )
            ->add((new Filter('shop', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Store', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('shop')
            )
            ->add((new Filter('currency', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Currency', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('currency')
            )
            ->add((new Filter('country', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Country', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('country')
            )
            ->add((new Filter('group_name', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Group', [], 'Admin.Global'),
                ],
            ])
            ->setAssociatedColumn('group_name')
            )
            ->add((new Filter('from_quantity', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('From quantity', [], 'Admin.Catalog.Feature'),
                ],
            ])
            ->setAssociatedColumn('from_quantity')
            )
            ->add((new Filter('reduction_type', ChoiceType::class))
            ->setTypeOptions([
                'required' => false,
                'choices' => [
                    $this->trans('Percentage', [], 'Admin.Global') => 'percentage',
                    $this->trans('Amount', [], 'Admin.Global') => 'amount',
                ],
            ])
            ->setAssociatedColumn('reduction_type')
            )
            ->add((new Filter('reduction', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Reduction', [], 'Admin.Catalog.Feature'),
                ],
            ])
            ->setAssociatedColumn('reduction')
            )
            ->add((new Filter('date_from', DateRangeType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Beginning', [], 'Admin.Catalog.Feature'),
                ],
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
            ])
            ->setAssociatedColumn('date_from')
            )
            ->add((new Filter('date_to', DateRangeType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('End', [], 'Admin.Catalog.Feature'),
                ],
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
            ])
            ->setAssociatedColumn('date_to')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setAssociatedColumn('actions')
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_catalog_price_rules_index',
            ])
            ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_selection'))
            ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => 'admin_catalog_price_rules_bulk_delete',
                'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
            ])
            );
    }
}
