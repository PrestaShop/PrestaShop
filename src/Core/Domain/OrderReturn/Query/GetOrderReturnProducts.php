<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

/**
 * Lists the products returned within an order return for rendering on the edit form.
 */
class GetOrderReturnProducts
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @param int $orderReturnId
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
    }

    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }
}
