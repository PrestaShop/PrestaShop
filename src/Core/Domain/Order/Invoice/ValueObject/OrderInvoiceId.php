<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Invoice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceException;

/**
 * Order invoice identity
 */
class OrderInvoiceId
{
    /**
     * @var int
     */
    private $orderInvoiceId;

    /**
     * @param int $orderInvoiceId
     */
    public function __construct($orderInvoiceId)
    {
        if (!is_int($orderInvoiceId) || 0 >= $orderInvoiceId) {
            throw new InvoiceException('Invalid order invoice id supplied.');
        }

        $this->orderInvoiceId = $orderInvoiceId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->orderInvoiceId;
    }
}
