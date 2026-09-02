<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class LinkedOrdersForViewing
{
    /** @var LinkedOrderForViewing[] */
    private $linkedOrders = [];

    /**
     * @param LinkedOrderForViewing[] $linkedOrders
     */
    public function __construct(array $linkedOrders)
    {
        foreach ($linkedOrders as $order) {
            $this->addLinkedOrder($order);
        }
    }

    /**
     * @return LinkedOrderForViewing[]
     */
    public function getLinkedOrders(): array
    {
        return $this->linkedOrders;
    }

    /**
     * @param LinkedOrderForViewing $order
     */
    private function addLinkedOrder(LinkedOrderForViewing $order): void
    {
        $this->linkedOrders[] = $order;
    }
}
