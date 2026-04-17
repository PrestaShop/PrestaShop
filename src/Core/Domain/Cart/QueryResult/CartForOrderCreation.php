<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartRule;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartSummary;

/**
 * Holds cart information data
 */
class CartForOrderCreation
{
    /**
     * @var int
     */
    private $cartId;

    /**
     * @var CartProduct[]
     */
    private $products;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var int
     */
    private $langId;

    /**
     * @var CartRule[]
     */
    private $cartRules;

    /**
     * @var CartAddress[]
     */
    private $addresses;

    /**
     * @var CartShipping|null
     */
    private $shipping;

    /**
     * @var CartSummary
     */
    private $summary;

    /**
     * @param int $cartId
     * @param array $products
     * @param int $currencyId
     * @param int $langId
     * @param CartRule[] $cartRules
     * @param CartAddress[] $addresses
     * @param CartSummary $summary
     * @param CartShipping $shipping
     */
    public function __construct(
        int $cartId,
        array $products,
        int $currencyId,
        int $langId,
        array $cartRules,
        array $addresses,
        CartSummary $summary,
        ?CartShipping $shipping = null
    ) {
        $this->cartId = $cartId;
        $this->products = $products;
        $this->currencyId = $currencyId;
        $this->langId = $langId;
        $this->cartRules = $cartRules;
        $this->addresses = $addresses;
        $this->shipping = $shipping;
        $this->summary = $summary;
    }

    /**
     * @return int
     */
    public function getCartId(): int
    {
        return $this->cartId;
    }

    /**
     * @return CartProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @return CartRule[]
     */
    public function getCartRules(): array
    {
        return $this->cartRules;
    }

    /**
     * @return CartAddress[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @return CartShipping|null
     */
    public function getShipping(): ?CartShipping
    {
        return $this->shipping;
    }

    /**
     * @return CartSummary
     */
    public function getSummary(): CartSummary
    {
        return $this->summary;
    }
}
