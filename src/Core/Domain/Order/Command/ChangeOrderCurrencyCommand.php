<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Changes currency for given order.
 */
class ChangeOrderCurrencyCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var CurrencyId
     */
    private $newCurrencyId;

    /**
     * @param int $orderId
     * @param int $newCurrencyId
     */
    public function __construct($orderId, $newCurrencyId)
    {
        $this->orderId = new OrderId($orderId);
        $this->newCurrencyId = new CurrencyId($newCurrencyId);
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return CurrencyId
     */
    public function getNewCurrencyId()
    {
        return $this->newCurrencyId;
    }
}
