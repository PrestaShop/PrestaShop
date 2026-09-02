<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

class CartView
{
    /**
     * @var array
     */
    private $customerInformation;

    /**
     * @var array
     */
    private $orderInformation;

    /**
     * @var int
     */
    private $cartId;

    /**
     * @var array
     */
    private $cartSummary;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @param int $cartId
     * @param int $currencyId
     * @param array $customerInformation
     * @param array $orderInformation
     * @param array $cartSummary
     */
    public function __construct(
        $cartId,
        $currencyId,
        array $customerInformation,
        array $orderInformation,
        array $cartSummary
    ) {
        $this->customerInformation = $customerInformation;
        $this->orderInformation = $orderInformation;
        $this->cartId = $cartId;
        $this->cartSummary = $cartSummary;
        $this->currencyId = $currencyId;
    }

    /**
     * @return array
     */
    public function getCustomerInformation()
    {
        return $this->customerInformation;
    }

    /**
     * @return array
     */
    public function getOrderInformation()
    {
        return $this->orderInformation;
    }

    /**
     * @return int
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return int
     */
    public function getCartCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @return array
     */
    public function getCartSummary()
    {
        return $this->cartSummary;
    }
}
