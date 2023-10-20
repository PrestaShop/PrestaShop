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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Currency\NameColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CurrencyGridDefinitionFactory is responsible for defining definition for currency list located in
 * "International -> Localization -> currencies".
 */
final class CurrencyGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'currency';

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
        return $this->trans('Currencies', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('currency_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_currency',
                    ])
            )
            ->add(
                (new DataColumn('id_currency'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_currency',
                    ])
            )
            ->add((new NameColumn('name'))
            ->setName($this->trans('Currency', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'name',
                'sortable' => false,
            ])
            )
            ->add((new DataColumn('symbol'))
            ->setName($this->trans('Symbol', [], 'Admin.International.Feature'))
            ->setOptions([
                'field' => 'symbol',
                'sortable' => false,
            ])
            )
            ->add((new DataColumn('iso_code'))
            ->setName($this->trans('ISO code', [], 'Admin.International.Feature'))
            ->setOptions([
                'field' => 'iso_code',
            ])
            )
            ->add((new DataColumn('conversion_rate'))
            ->setName($this->trans('Exchange rate', [], 'Admin.International.Feature'))
            ->setOptions([
                'field' => 'conversion_rate',
            ])
            )
            ->add((new ToggleColumn('active'))
            ->setName($this->trans('Enabled', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'active',
                'primary_field' => 'id_currency',
                'route' => 'admin_currencies_toggle_status',
                'route_param_name' => 'currencyId',
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => (new RowActionCollection())
                    ->add((new LinkRowAction('edit'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_currencies_edit',
                        'route_param_name' => 'currencyId',
                        'route_param_field' => 'id_currency',
                        'clickable_row' => true,
                    ])
                    )
                    ->add((new SubmitRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'method' => 'DELETE',
                        'route' => 'admin_currencies_delete',
                        'route_param_name' => 'currencyId',
                        'route_param_field' => 'id_currency',
                        'confirm_message' => $this->trans(
                            'Delete selected item?',
                            [],
                            'Admin.Notifications.Warning'
                        ),
                    ])
                    )
                    ->add(
                        $this->buildDeleteAction(
                            'admin_currencies_delete',
                            'currencyId',
                            'id_currency',
                            Request::METHOD_DELETE
                        )
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
            ->add(
                 (new Filter('id_currency', NumberType::class))
                     ->setTypeOptions([
                         'required' => false,
                         'attr' => [
                             'placeholder' => $this->translator->trans('Search ID', [], 'Admin.Actions'),
                         ],
                     ])
                     ->setAssociatedColumn('id_currency')
             )
            ->add(
                 (new Filter('name', TextType::class))
                     ->setTypeOptions([
                         'required' => false,
                         'attr' => [
                             'placeholder' => $this->translator->trans('Currency', [], 'Admin.Global'),
                         ],
                     ])
                     ->setAssociatedColumn('name')
             )
            ->add(
                 (new Filter('symbol', TextType::class))
                     ->setTypeOptions([
                         'required' => false,
                         'attr' => [
                             'placeholder' => $this->translator->trans('Symbol', [], 'Admin.International.Feature'),
                         ],
                     ])
                     ->setAssociatedColumn('symbol')
             )
            ->add((new Filter('iso_code', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                ],
            ])
            ->setAssociatedColumn('iso_code')
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
            ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_currencies_index',
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
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_currencies_bulk_toggle_status',
                        'route_params' => [
                            'status' => 'enable',
                        ],
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_currencies_bulk_toggle_status',
                        'route_params' => [
                            'status' => 'disable',
                        ],
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_currencies_bulk_delete')
            );
    }
}
