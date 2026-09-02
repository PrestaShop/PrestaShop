<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerCartInformation.
 */
class CartInformation
{
    /**
     * @var string
     */
    private $cartId;

    /**
     * @var string
     */
    private $cartCreationDate;

    /**
     * @var string
     */
    private $cartTotal;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @param string $cartId
     * @param string $cartCreationDate
     * @param string $cartTotal
     * @param string $carrierName
     */
    public function __construct(
        $cartId,
        $cartCreationDate,
        $cartTotal,
        $carrierName
    ) {
        $this->cartId = $cartId;
        $this->cartCreationDate = $cartCreationDate;
        $this->cartTotal = $cartTotal;
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function getCartCreationDate()
    {
        return $this->cartCreationDate;
    }

    /**
     * @return string
     */
    public function getCartTotal()
    {
        return $this->cartTotal;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }
}
