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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        return (new RowActionCollection())
            ->add(
                (new AdditionalShipmentRowAction('More'))
                    ->setName($this->trans('More', [], 'Admin.Actions'))
                    ->setIcon('more_vert')
                    ->setOptions([
                        'shipment_id_field' => 'shipment_number',
                        'order_id_field' => 'order_id',
                        'items' => 'items',
                        'total_shipments' => 'total_shipments',
                    ]));
    }
}
