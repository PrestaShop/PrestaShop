<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

/**
 * Holds the totals of a cart shown in the carts grids (the full total and the products-only total).
 */
class CartTotalForViewing
{
    /**
     * @var float
     */
    private $total;

    /**
     * @var float
     */
    private $totalProducts;

    /**
     * @param float $total
     * @param float $totalProducts
     */
    public function __construct($total, $totalProducts)
    {
        $this->total = $total;
        $this->totalProducts = $totalProducts;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return float
     */
    public function getTotalProducts()
    {
        return $this->totalProducts;
    }
}
