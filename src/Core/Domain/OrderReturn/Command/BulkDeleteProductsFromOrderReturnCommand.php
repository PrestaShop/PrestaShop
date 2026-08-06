<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnProductId;

/**
 * Removes several product rows from a merchandise return atomically, in the same form submit
 * as the rest of the edit page. Backs the deferred-delete UI introduced by Issue #27628.
 */
class BulkDeleteProductsFromOrderReturnCommand
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderReturnProductId[]
     */
    private $productIds;

    /**
     * @param array<int, array{order_detail_id: int, customization_id?: int}> $stagedProductRows
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId, array $stagedProductRows)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
        $this->productIds = [];
        foreach ($stagedProductRows as $row) {
            $this->productIds[] = new OrderReturnProductId(
                (int) $row['order_detail_id'],
                (int) ($row['customization_id'] ?? 0)
            );
        }
    }

    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    /**
     * @return OrderReturnProductId[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
