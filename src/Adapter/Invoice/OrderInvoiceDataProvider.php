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
     * @param $dateFrom
     * @param $dateTo
     *
     * @return array collection of OrderInvoice objects
     */
    public function getByDateInterval($dateFrom, $dateTo)
    {
        return OrderInvoice::getByDateInterval($dateFrom, $dateTo);
    }
}
