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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ButtonBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ModalFormSubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ChoiceColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DisableableLinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PreviewColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Order\OrderPriceColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Creates definition for Orders grid
 */
final class OrderGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    public const GRID_ID = 'order';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderCountriesChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatusesChoiceProvider;

    /**
     * @var string
     */
    private $contextDateFormat;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatesChoiceProvider;

    /**
     * @var AccessibilityCheckerInterface
     */
    private $printInvoiceAccessibilityChecker;

    /**
     * @var AccessibilityCheckerInterface
     */
    private $printDeliverySlipAccessibilityChecker;

    /**
     * @param HookDispatcherInterface $dispatcher
     * @param ConfigurationInterface $configuration
     * @param FormChoiceProviderInterface $orderCountriesChoiceProvider
     * @param FormChoiceProviderInterface $orderStatusesChoiceProvider
     * @param string $contextDateFormat
     * @param FeatureInterface $multistoreFeature
     * @param AccessibilityCheckerInterface $printInvoiceAccessibilityChecker
     * @param AccessibilityCheckerInterface $printDeliverySlipAccessibilityChecker
     * @param FormChoiceProviderInterface $orderStatesChoiceProvider
     */
    public function __construct(
        HookDispatcherInterface $dispatcher,
        ConfigurationInterface $configuration,
        FormChoiceProviderInterface $orderCountriesChoiceProvider,
        FormChoiceProviderInterface $orderStatusesChoiceProvider,
        $contextDateFormat,
        FeatureInterface $multistoreFeature,
        AccessibilityCheckerInterface $printInvoiceAccessibilityChecker,
        AccessibilityCheckerInterface $printDeliverySlipAccessibilityChecker,
        FormChoiceProviderInterface $orderStatesChoiceProvider
    ) {
        parent::__construct($dispatcher);

        $this->configuration = $configuration;
        $this->orderCountriesChoiceProvider = $orderCountriesChoiceProvider;
        $this->orderStatusesChoiceProvider = $orderStatusesChoiceProvider;
        $this->contextDateFormat = $contextDateFormat;
        $this->multistoreFeature = $multistoreFeature;
        $this->printInvoiceAccessibilityChecker = $printInvoiceAccessibilityChecker;
        $this->printDeliverySlipAccessibilityChecker = $printDeliverySlipAccessibilityChecker;
        $this->orderStatesChoiceProvider = $orderStatesChoiceProvider;
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
        return $this->trans('Orders', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $previewColumn = (new PreviewColumn('preview'))
            ->setOptions([
                'icon_expand' => 'keyboard_arrow_down',
                'icon_collapse' => 'keyboard_arrow_up',
                'preview_data_route' => 'admin_orders_preview',
                'preview_route_params' => [
                    'orderId' => 'id_order',
                ],
            ])
        ;

        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('orders_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_order',
                    ])
            )
            ->add((new IdentifierColumn('id_order'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'identifier_field' => 'id_order',
                'preview' => $previewColumn,
                'clickable' => false,
            ])
            )
            ->add((new DataColumn('reference'))
            ->setName($this->trans('Reference', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'reference',
            ])
            )
            ->add((new BooleanColumn('new'))
            ->setName($this->trans('New client', [], 'Admin.Orderscustomers.Feature'))
            ->setOptions([
                'field' => 'new',
                'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                'false_name' => $this->trans('No', [], 'Admin.Global'),
                'clickable' => true,
            ])
            )
            ->add((new DisableableLinkColumn('customer'))
            ->setName($this->trans('Customer', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'customer',
                'disabled_field' => 'deleted_customer',
                'route' => 'admin_customers_view',
                'route_param_name' => 'customerId',
                'route_param_field' => 'id_customer',
                'target' => '_blank',
            ])
            )
            ->add((new OrderPriceColumn('total_paid_tax_incl'))
            ->setName($this->trans('Total', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'total_paid_tax_incl',
                'is_paid_field' => 'paid',
                'clickable' => true,
            ])
            )
            ->add((new DataColumn('payment'))
            ->setName($this->trans('Payment', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'payment',
            ])
            )
            ->add((new ChoiceColumn('osname'))
            ->setName($this->trans('Status', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'current_state',
                'route' => 'admin_orders_list_update_status',
                'color_field' => 'color',
                'choice_provider' => $this->orderStatesChoiceProvider,
                'record_route_params' => [
                    'id_order' => 'orderId',
                ],
            ])
            )
            ->add((new DateTimeColumn('date_add'))
            ->setName($this->trans('Date', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'date_add',
                'format' => $this->contextDateFormat,
                'clickable' => true,
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => $this->getRowActions(),
            ])
            )
        ;

        if ($this->orderCountriesChoiceProvider->getChoices()) {
            $columns->addAfter('new', (new DataColumn('country_name'))
                ->setName($this->trans('Delivery', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'country_name',
                ])
            );
        }

        if ($this->configuration->get('PS_B2B_ENABLE')) {
            $columns->addAfter('customer', (new DataColumn('company'))
                ->setName($this->trans('Company', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'company',
                ])
            );
        }

        if ($this->multistoreFeature->isUsed()) {
            $columns->addBefore('actions', (new DataColumn('shop_name'))
                ->setName($this->trans('Store', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'shop_name',
                    'sortable' => false,
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = new FilterCollection();

        $filters
            ->add((new Filter('id_order', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('id_order')
            )
            ->add((new Filter('reference', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search reference', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('reference')
            )
            ->add(
                (new Filter('new', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('new')
            )
            ->add((new Filter('customer', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search customer', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('customer')
            )
            ->add((new Filter('total_paid_tax_incl', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search total', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('total_paid_tax_incl')
            )
            ->add((new Filter('payment', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search payment', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('payment')
            )
            ->add((new Filter('osname', ChoiceType::class))
            ->setTypeOptions([
                'required' => false,
                'choices' => $this->orderStatusesChoiceProvider->getChoices(),
                'translation_domain' => false,
            ])
            ->setAssociatedColumn('osname')
            )
            ->add((new Filter('date_add', DateRangeType::class))
            ->setTypeOptions([
                'required' => false,
            ])
            ->setAssociatedColumn('date_add')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_orders_index',
            ])
            ->setAssociatedColumn('actions')
            )
        ;

        $orderCountriesChoices = $this->orderCountriesChoiceProvider->getChoices();

        if (!empty($orderCountriesChoices)) {
            $filters->add((new Filter('country_name', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $orderCountriesChoices,
                ])
                ->setAssociatedColumn('country_name')
            );
        }

        if ($this->configuration->get('PS_B2B_ENABLE')) {
            $filters->add((new Filter('company', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search company', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('company')
            );
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', [], 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'admin_orders_export',
                    ])
            )
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
            ->add((new ModalFormSubmitBulkAction('change_order_status'))
            ->setName($this->trans('Change Order Status', [], 'Admin.Orderscustomers.Feature'))
            ->setOptions([
                'submit_route' => 'admin_orders_change_orders_status',
                'modal_id' => 'changeOrdersStatusModal',
            ])
            )
            ->add((new ButtonBulkAction('open_tabs'))
            ->setName($this->trans('Open in new tabs', [], 'Admin.Orderscustomers.Feature'))
            ->setOptions([
                'class' => 'open_tabs',
                'attributes' => [
                    'data-route' => 'admin_orders_view',
                    'data-route-param-name' => 'orderId',
                    'data-tabs-blocked-message' => $this->trans(
                        'It looks like you have exceeded the number of tabs allowed. Check your browser settings to open multiple tabs.',
                        [],
                        'Admin.Orderscustomers.Feature'
                    ),
                ],
            ])
            )
        ;
    }

    /**
     * @return RowActionCollection
     */
    private function getRowActions(): RowActionCollection
    {
        $rowActionCollection = new RowActionCollection();
        $rowActionCollection->add(
                (new LinkRowAction('print_invoice'))
                    ->setName($this->trans('View invoice', [], 'Admin.Orderscustomers.Feature'))
                    ->setIcon('receipt')
                    ->setOptions([
                        'accessibility_checker' => $this->printInvoiceAccessibilityChecker,
                        'route' => 'admin_orders_generate_invoice_pdf',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                        'use_inline_display' => true,
                    ])
            )
            ->add(
                (new LinkRowAction('print_delivery_slip'))
                    ->setName($this->trans('View delivery slip', [], 'Admin.Orderscustomers.Feature'))
                    ->setIcon('local_shipping')
                    ->setOptions([
                        'accessibility_checker' => $this->printDeliverySlipAccessibilityChecker,
                        'route' => 'admin_orders_generate_delivery_slip_pdf',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                        'use_inline_display' => true,
                    ])
            )
            ->add(
                (new LinkRowAction('view'))
                    ->setName($this->trans('View', [], 'Admin.Actions'))
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => 'admin_orders_view',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                        'use_inline_display' => true,
                        'clickable_row' => true,
                    ])
            )
        ;

        return $rowActionCollection;
    }
}
