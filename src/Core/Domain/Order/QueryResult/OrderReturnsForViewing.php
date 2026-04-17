<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderReturnsForViewing
{
    /** @var OrderReturnForViewing[] */
    private $orderReturns = [];

    /**
     * @param OrderReturnForViewing[] $orderReturns
     */
    public function __construct(array $orderReturns = [])
    {
        foreach ($orderReturns as $orderReturn) {
            $this->add($orderReturn);
        }
    }

    /**
     * @return OrderReturnForViewing[]
     */
    public function getOrderReturns(): array
    {
        return $this->orderReturns;
    }

    /**
     * @param OrderReturnForViewing $orderReturn
     */
    private function add(OrderReturnForViewing $orderReturn): void
    {
        $this->orderReturns[] = $orderReturn;
    }
}
