<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerOrders.
 */
class OrdersInformation
{
    /**
     * @var array|OrderInformation[]
     */
    private $validOrders;

    /**
     * @var array|OrderInformation[]
     */
    private $invalidOrders;

    /**
     * @var string
     */
    private $totalSpent;

    /**
     * @param string $totalSpent
     * @param OrderInformation[] $validOrders
     * @param OrderInformation[] $invalidOrders
     */
    public function __construct($totalSpent, array $validOrders, array $invalidOrders)
    {
        $this->validOrders = $validOrders;
        $this->invalidOrders = $invalidOrders;
        $this->totalSpent = $totalSpent;
    }

    /**
     * @return OrderInformation[]
     */
    public function getValidOrders()
    {
        return $this->validOrders;
    }

    /**
     * @return OrderInformation[]
     */
    public function getInvalidOrders()
    {
        return $this->invalidOrders;
    }

    /**
     * @return string
     */
    public function getTotalSpent()
    {
        return $this->totalSpent;
    }
}
