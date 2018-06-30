<?php

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use DateTimeInterface;
use OrderInvoice;
use PrestaShop\PrestaShop\Core\DataProvider\OrderInvoiceDataProviderInterface;

/**
 * Class OrderInvoiceDataProvider provides OrderInvoice data using legacy code
 */
final class OrderInvoiceDataProvider implements OrderInvoiceDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByDateInterval(DateTimeInterface $dateFrom, DateTimeInterface $dateTo)
    {
        return OrderInvoice::getByDateInterval(
            $dateFrom->format('Y-m-d'),
            $dateTo->format('Y-m-d')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByStatus($orderStateId)
    {
        return OrderInvoice::getByStatus($orderStateId);
    }
}
