<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;

/**
 * Identifies one product row within a merchandise return.
 *
 * A product row is uniquely identified by the pair (id_order_detail, id_customization)
 * — the composite primary key of `order_return_detail`. A customization id of `0`
 * means the row is a regular (non-customized) product line.
 */
class OrderReturnProductId
{
    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var int
     */
    private $customizationId;

    /**
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderDetailId, int $customizationId = 0)
    {
        if ($orderDetailId <= 0) {
            throw new OrderReturnConstraintException(
                sprintf('Invalid order detail id "%d" for an order return product row.', $orderDetailId),
                OrderReturnConstraintException::INVALID_ORDER_DETAIL_ID
            );
        }
        if ($customizationId < 0) {
            throw new OrderReturnConstraintException(
                sprintf('Invalid customization id "%d" for an order return product row.', $customizationId),
                OrderReturnConstraintException::INVALID_CUSTOMIZATION_ID
            );
        }
        $this->orderDetailId = $orderDetailId;
        $this->customizationId = $customizationId;
    }

    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    public function getCustomizationId(): int
    {
        return $this->customizationId;
    }
}
