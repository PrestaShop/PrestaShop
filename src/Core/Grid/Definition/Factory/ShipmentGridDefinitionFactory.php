<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment\AdditionalShipmentRowAction;
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
        private LanguageContext $languageContext
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

    /**
     * @return RowActionCollectionInterface
     */
    private function getRowActions()
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add(
                (new AdditionalShipmentRowAction('More'))
                    ->setName($this->trans('More', [], 'Admin.Actions'))
                    ->setIcon('more_vert')
                    ->setOptions([
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                        'items' => 'items',
                        'total_shipments' => 'total_shipments',
                        'tracking_number' => 'tracking_number',
                        'carrier' => 'carrier',
                    ])
            );

        return $rowActions;
    }
}
