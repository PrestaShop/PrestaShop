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
 * Removes a single product row from a merchandise return.
 *
 * Handler enforces the legacy parity rule: the last remaining product line cannot be deleted.
 */
class DeleteProductFromOrderReturnCommand
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderReturnProductId
     */
    private $productId;

    /**
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId, int $orderDetailId, int $customizationId = 0)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
        $this->productId = new OrderReturnProductId($orderDetailId, $customizationId);
    }

    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    public function getProductId(): OrderReturnProductId
    {
        return $this->productId;
    }
}
