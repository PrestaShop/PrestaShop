<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Throwable;

/**
 * Class DuplicateProductInOrderInvoiceException thrown when we try to add a product in an order invoice that already contains it
 */
class DuplicateProductInOrderInvoiceException extends DuplicateProductInOrderException
{
    /**
     * @var string
     */
    private $orderInvoiceNumber;

    /**
     * @param string $orderInvoiceId
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $orderInvoiceId, $message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->orderInvoiceNumber = $orderInvoiceId;
    }

    /**
     * @return string
     */
    public function getOrderInvoiceNumber(): string
    {
        return $this->orderInvoiceNumber;
    }
}
