<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\DeliverySlipShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines the shop-wide shipments grid, listing every shipment across all orders.
 *
 * The per-order shipments grid is a different one, see OrderShipmentGridDefinitionFactory.
 *
 * @internal The grid id 'shipment' was the per-order shipments grid up to 9.2, and it now names this
 *           shop-wide one. The per-order grid moved to 'order_shipment'. That reassignment also moves
 *           the hooks derived from the id, actionShipmentGrid*Modifier, onto this grid. It is a
 *           deliberate break, accepted because the improved_shipment feature flag is beta and off by
 *           default; these classes carry no backward compatibility promise while it stays that way.
 */
final class ShipmentGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    public const GRID_ID = 'shipment';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly LanguageContext $languageContext,
        private readonly FeatureInterface $multistoreFeature,
        private readonly FormChoiceProviderInterface $shipmentStatusChoiceProvider,
        private readonly FormChoiceProviderInterface $shipmentCarrierChoiceProvider,
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
        return $this->trans('Shipments', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_shipment',
                ])
            )
            ->add((new IdentifierColumn('id_shipment'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'identifier_field' => 'id_shipment',
                    'clickable' => false,
                ])
            )
            ->add((new LinkColumn('order_reference'))
                ->setName($this->trans('Order', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'order_reference',
                    'route' => 'admin_orders_view',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                ])
            )
            ->add((new DataColumn('customer'))
                ->setName($this->trans('Customer', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'customer',
                ])
            )
            ->add((new DataColumn('carrier'))
                ->setName($this->trans('Carrier', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'carrier',
                ])
            )
            ->add((new DataColumn('tracking_number'))
                ->setName($this->trans('Tracking number', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'tracking_number',
                ])
            )
            ->add((new BadgeColumn('status'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'status',
                    // Emptied so that the per-record type set by ShipmentGridDataFactory wins.
                    'badge_type' => '',
                    'badge_type_field' => 'status_badge_type',
                    'alignment' => 'left',
                ])
            )
            ->add((new DataColumn('items'))
                ->setName($this->trans('Items', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'items',
                    'alignment' => 'right',
                ])
            )
            ->add((new DataColumn('weight'))
                ->setName($this->trans('Weight', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'weight',
                    'sortable' => false,
                    'alignment' => 'right',
                ])
            )
            ->add((new DataColumn('shipping_cost'))
                ->setName($this->trans('Shipping cost', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'shipping_cost',
                    'alignment' => 'right',
                ])
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Created', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date_add',
                    'format' => $this->languageContext->getDateTimeFormat(),
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => $this->getRowActions(),
                ])
            )
        ;

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
     *
     * @throws DBALException if the carrier choices cannot be read from the database
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_shipment', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_shipment')
            )
            ->add((new Filter('order_reference', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search reference', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('order_reference')
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
            ->add((new Filter('carrier', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $this->shipmentCarrierChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'placeholder' => $this->trans('All carriers', [], 'Admin.Global'),
                ])
                ->setAssociatedColumn('carrier')
            )
            ->add((new Filter('tracking_number', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search tracking number', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('tracking_number')
            )
            ->add((new Filter('status', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $this->shipmentStatusChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'placeholder' => $this->trans('All statuses', [], 'Admin.Global'),
                ])
                ->setAssociatedColumn('status')
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
                    'redirect_route' => 'admin_shipments_index',
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
            ->add((new SubmitBulkAction('download_delivery_slips'))
                ->setName($this->trans('Download delivery slips', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'submit_route' => 'admin_shipments_generate_delivery_slips_pdf',
                ])
            )
        ;
    }

    private function getRowActions(): RowActionCollectionInterface
    {
        return (new RowActionCollection())
            ->add((new LinkRowAction('view_order'))
                ->setName($this->trans('View order', [], 'Admin.Actions'))
                ->setIcon('zoom_in')
                ->setOptions([
                    'route' => 'admin_orders_view',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                    'clickable_row' => true,
                ])
            )
            ->add((new DeliverySlipShipmentRowAction('print_delivery_slip'))
                ->setName($this->trans('Download delivery slip', [], 'Admin.Orderscustomers.Feature'))
                ->setIcon('local_shipping')
                ->setOptions([
                    'route' => 'admin_orders_generate_shipment_delivery_slip_pdf',
                    'route_param_name' => 'orderId',
                    'route_param_field' => 'id_order',
                    'extra_route_params' => [
                        'shipmentId' => 'id_shipment',
                    ],
                ])
            )
        ;
    }
}
