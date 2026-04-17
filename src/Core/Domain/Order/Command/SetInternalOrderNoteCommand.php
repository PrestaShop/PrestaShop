<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Sets internal note about order that can only be seen in Back Office.
 */
class SetInternalOrderNoteCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var string
     */
    private $internalNote;

    /**
     * @param int $orderId
     * @param string $internalNote
     */
    public function __construct($orderId, $internalNote)
    {
        $this->assertInternalNoteIsString($internalNote);

        $this->orderId = new OrderId($orderId);
        $this->internalNote = $internalNote;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getInternalNote()
    {
        return $this->internalNote;
    }

    /**
     * @param string $internalNote
     *
     * @throws OrderConstraintException
     */
    private function assertInternalNoteIsString($internalNote)
    {
        if (!is_string($internalNote)) {
            throw new OrderConstraintException('Invalid internal note provided. Internal note must be a string.', OrderConstraintException::INVALID_INTERNAL_NOTE);
        }
    }
}
