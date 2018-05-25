<?php

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use OrderInvoice;

/**
 * Class OrderInvoiceDataProvider provides OrderInvoice data using legacy code
 */
class OrderInvoiceDataProvider
{
    /**
     * Returns all the order invoices that match the date interval
     *
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return array collection of OrderInvoice objects
     */
    public function getByDateInterval($dateFrom, $dateTo)
    {
        return OrderInvoice::getByDateInterval($dateFrom, $dateTo);
    }

    /**
     * Returns all the order invoices by given status
     *
     * @param int $orderStateId
     *
     * @return array collection of OrderInvoice objects
     */
    public function getByStatus($orderStateId)
    {
        return OrderInvoice::getByStatus($orderStateId);
    }
}
