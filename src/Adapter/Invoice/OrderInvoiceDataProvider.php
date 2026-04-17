<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use DateTimeInterface;
use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShop\PrestaShop\Adapter\Entity\OrderInvoice;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;

/**
 * Class OrderInvoiceDataProvider provides OrderInvoice data using legacy code.
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

    /**
     * {@inheritdoc}
     */
    public function getNextInvoiceNumber()
    {
        return Order::getLastInvoiceNumber() + 1;
    }
}
