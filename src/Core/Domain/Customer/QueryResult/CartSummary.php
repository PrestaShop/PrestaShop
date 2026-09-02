<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Holds data of summarized cart
 */
class CartSummary
{
    /**
     * @var int
     */
    private $cartId;

    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var string
     */
    private $totalPrice;

    /**
     * @param int $cartId
     * @param string $creationDate
     * @param string $totalPrice
     */
    public function __construct(
        int $cartId,
        string $creationDate,
        string $totalPrice
    ) {
        $this->cartId = $cartId;
        $this->creationDate = $creationDate;
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return int
     */
    public function getCartId(): int
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    /**
     * @return string
     */
    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }
}
