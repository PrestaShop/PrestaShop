<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\DeliverySlipShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\EditShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\FulfillShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\MergeShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\SplitShipmentRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

final class ShipmentGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    public const GRID_ID = 'shipment';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private LanguageContext $languageContext,
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
            ->add((new DateTimeColumn('date'))
                ->setName($this->trans('Date', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date',
                    'format' => $this->languageContext->getDateTimeFormat(),
                    'clickable' => true,
                    'sortable' => false,
                    'alignment' => 'left',
                ])
            )
            ->add(
                (new IdentifierColumn('shipment_number'))
                    ->setName($this->trans('Shipment number', [], 'Admin.Global'))
                    ->setOptions([
                        'identifier_field' => 'shipment_number',
                        'bulk_field' => 'shipment_number',
                        'with_bulk_field' => false,
                        'sortable' => false,
                        'clickable' => false,
                    ])
            )
            ->add(
                (new DataColumn('carrier'))
                    ->setName($this->trans('Carrier', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'carrier',
                        'sortable' => false,
                        'alignment' => 'left',
                    ])
            )
            ->add(
                (new DataColumn('items'))
                    ->setName($this->trans('Items', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'items',
                        'sortable' => false,
                        'alignment' => 'right',
                    ])
            )
            ->add(
                (new DataColumn('shipping_cost'))
                    ->setName($this->trans('Shipping cost', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shipping_cost',
                        'sortable' => false,
                        'alignment' => 'right',
                    ])
            )
            ->add(
                (new DataColumn('weight'))
                    ->setName($this->trans('Weight', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'weight',
                        'sortable' => false,
                        'alignment' => 'left',
                    ])
            )
            ->add(
                (new DataColumn('tracking_number'))
                    ->setName($this->trans('Tracking number', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'tracking_number',
                        'sortable' => false,
                        'alignment' => 'left',
                    ])
            )->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            );

        return $columns;
    }

    private function getRowActions(): RowActionCollectionInterface
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add(
                (new EditShipmentRowAction('Edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'tracking_number' => 'tracking_number',
                        'carrier' => 'carrier',
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                    ])
            )
            ->add(
                (new DeliverySlipShipmentRowAction('print_delivery_slip'))
                    ->setName($this->trans('Download delivery slip', [], 'Admin.Orderscustomers.Feature'))
                    ->setIcon('local_shipping')
                    ->setOptions([
                        'route' => 'admin_orders_generate_shipment_delivery_slip_pdf',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'order_id',
                        'extra_route_params' => [
                            'shipmentId' => 'shipment_number',
                        ],
                    ])
            )
            ->add(
                (new FulfillShipmentRowAction('fulfill'))
                    ->setName($this->trans('Fulfill', [], 'Admin.Actions'))
                    ->setIcon('package_2')
                    ->setOptions([
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                    ])
            )
            ->add(
                (new SplitShipmentRowAction('split'))
                    ->setName($this->trans('Split', [], 'Admin.Actions'))
                    ->setIcon('call_split')
                    ->setOptions([
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                        'items' => 'items',
                    ])
            )
            ->add(
                (new MergeShipmentRowAction('merge'))
                    ->setName($this->trans('Merge', [], 'Admin.Actions'))
                    ->setIcon('call_merge')
                    ->setOptions([
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                        'total_shipments' => 'total_shipments',
                    ])
            );

        return $rowActions;
    }
}
