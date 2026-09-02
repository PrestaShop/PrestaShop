<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds cart summary data
 */
class CartSummary
{
    /**
     * @param string $totalProductsPrice
     * @param string $totalDiscount
     * @param string $totalShippingPrice
     * @param string $totalShippingWithoutTaxes
     * @param string $totalTaxes
     * @param string $totalPriceWithTaxes
     * @param string $totalPriceWithoutTaxes
     * @param string $orderMessage
     * @param string $processOrderLink
     */
    public function __construct(
        private string $totalProductsPrice,
        private string $totalDiscount,
        private string $totalShippingPrice,
        private string $totalShippingWithoutTaxes,
        private string $totalTaxes,
        private string $totalPriceWithTaxes,
        private string $totalPriceWithoutTaxes,
        private string $orderMessage,
        private string $processOrderLink,
    ) {
    }

    /**
     * @return string
     */
    public function getTotalProductsPrice(): string
    {
        return $this->totalProductsPrice;
    }

    public function getTotalDiscount(): string
    {
        return $this->totalDiscount;
    }

    public function getTotalShippingPrice(): string
    {
        return $this->totalShippingPrice;
    }

    public function getTotalShippingWithoutTaxes(): string
    {
        return $this->totalShippingWithoutTaxes;
    }

    public function getTotalTaxes(): string
    {
        return $this->totalTaxes;
    }

    public function getTotalPriceWithTaxes(): string
    {
        return $this->totalPriceWithTaxes;
    }

    public function getTotalPriceWithoutTaxes(): string
    {
        return $this->totalPriceWithoutTaxes;
    }

    public function getProcessOrderLink(): string
    {
        return $this->processOrderLink;
    }

    public function getOrderMessage(): string
    {
        return $this->orderMessage;
    }
}
