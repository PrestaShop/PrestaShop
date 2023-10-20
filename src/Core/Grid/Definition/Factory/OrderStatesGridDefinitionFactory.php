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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ColorColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderStatesGridDefinitionFactory defines order_states grid structure.
 */
final class OrderStatesGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'order_states';

    /**
     * @var AccessibilityCheckerInterface
     */
    protected $deleteOrderStatesAccessibilityChecker;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        AccessibilityCheckerInterface $deleteOrderStatesAccessibilityChecker
    ) {
        parent::__construct($hookDispatcher);

        $this->deleteOrderStatesAccessibilityChecker = $deleteOrderStatesAccessibilityChecker;
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
        return $this->trans('Order statuses', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('order_states_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_order_state',
                        'disabled_field' => 'unremovable',
                    ])
            )
            ->add(
                (new DataColumn('id_order_state'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_order_state',
                    ])
            )
            ->add(
                (new ColorColumn('name'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                        'color_field' => 'color',
                    ])
            )
            ->add(
                (new ToggleColumn('send_email'))
                    ->setName($this->trans('Send email to customer', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'send_email',
                        'primary_field' => 'id_order_state',
                        'route' => 'admin_order_states_toggle_send_email',
                        'route_param_name' => 'orderStateId',
                    ])
            )
            ->add(
                (new ToggleColumn('delivery'))
                    ->setName($this->trans('In transit', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'delivery',
                        'primary_field' => 'id_order_state',
                        'route' => 'admin_order_states_toggle_delivery',
                        'route_param_name' => 'orderStateId',
                    ])
            )
            ->add(
                (new ToggleColumn('invoice'))
                    ->setName($this->trans('Invoice', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'invoice',
                        'primary_field' => 'id_order_state',
                        'route' => 'admin_order_states_toggle_invoice',
                        'route_param_name' => 'orderStateId',
                    ])
            )
            ->add(
                (new DataColumn('template'))
                    ->setName($this->trans('Email template', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'template',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_order_states_edit',
                                        'route_param_name' => 'orderStateId',
                                        'route_param_field' => 'id_order_state',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_order_states_delete',
                                    'orderStateId',
                                    'id_order_state',
                                    Request::METHOD_DELETE,
                                    [],
                                    [
                                        'accessibility_checker' => $this->deleteOrderStatesAccessibilityChecker,
                                    ]
                                )
                            ),
                    ])
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_order_state', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_order_state')
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
                (new Filter('send_email', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('send_email')
            )
            ->add(
                (new Filter('delivery', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('delivery')
            )
            ->add(
                (new Filter('invoice', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('invoice')
            )
            ->add(
                (new Filter('template', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search template', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('template')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_order_states',
                    ])
                    ->setAssociatedColumn('actions')
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
    protected function getBulkActions(): BulkActionCollectionInterface
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_order_states_delete_bulk')
            );
    }
}
