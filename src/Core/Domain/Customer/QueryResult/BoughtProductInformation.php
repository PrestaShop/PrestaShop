<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class BoughtProductInformation holds information about product that customer has bought.
 */
class BoughtProductInformation
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $boughtDate;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var int
     */
    private $boughtQuantity;

    /**
     * @param int $orderId
     * @param string $boughtDate
     * @param string $productName
     * @param int $boughtQuantity
     */
    public function __construct(
        $orderId,
        $boughtDate,
        $productName,
        $boughtQuantity
    ) {
        $this->orderId = $orderId;
        $this->boughtDate = $boughtDate;
        $this->productName = $productName;
        $this->boughtQuantity = $boughtQuantity;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getBoughtDate()
    {
        return $this->boughtDate;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getBoughtQuantity()
    {
        return $this->boughtQuantity;
    }
}
