<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Order;

use OrderInvoice;

interface InvoiceInterface
{
    /**
     * Return collection of Invoice.
     *
     * @param string $dateFrom Date From
     * @param string $dateTo Date To
     *
     * @return array<OrderInvoice>
     */
    public static function getByDeliveryDateInterval($dateFrom, $dateTo);
}
