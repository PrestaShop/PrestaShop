<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Order;

use DateTimeInterface;

/**
 * Interface OrderInvoiceDataProviderInterface defines OrderInvoice data provider.
 */
interface OrderInvoiceDataProviderInterface
{
    /**
     * Returns all the order invoices that match the date interval.
     *
     * @param DateTimeInterface $dateFrom
     * @param DateTimeInterface $dateTo
     *
     * @return array collection of OrderInvoice objects
     */
    public function getByDateInterval(DateTimeInterface $dateFrom, DateTimeInterface $dateTo);

    /**
     * Returns all the order invoices by given status.
     *
     * @param int $orderStateId
     *
     * @return array collection of OrderInvoice objects
     */
    public function getByStatus($orderStateId);

    /**
     * Returns the next available invoice number.
     *
     * @return int
     */
    public function getNextInvoiceNumber();
}
