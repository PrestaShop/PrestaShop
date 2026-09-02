<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\ValueObject\OrderInvoiceId;

/**
 * Adds note for given invoice.
 */
class UpdateInvoiceNoteCommand
{
    /**
     * @var OrderInvoiceId
     */
    private $orderInvoiceId;

    /**
     * @var string|null
     */
    private $note;

    /**
     * @param int $orderInvoiceId
     * @param string|null $note
     */
    public function __construct(int $orderInvoiceId, ?string $note)
    {
        $this->orderInvoiceId = new OrderInvoiceId($orderInvoiceId);
        $this->note = $note;
    }

    /**
     * @return OrderInvoiceId
     */
    public function getOrderInvoiceId(): OrderInvoiceId
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }
}
