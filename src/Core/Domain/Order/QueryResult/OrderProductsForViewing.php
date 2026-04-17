<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderProductsForViewing
{
    /**
     * @var OrderProductForViewing[]
     */
    private $products = [];

    /**
     * @param OrderProductForViewing[] $products
     */
    public function __construct(array $products)
    {
        foreach ($products as $product) {
            $this->addProductForViewing($product);
        }
    }

    /**
     * @param OrderProductForViewing $productForViewing
     */
    private function addProductForViewing(OrderProductForViewing $productForViewing): void
    {
        $this->products[] = $productForViewing;
    }

    /**
     * @return OrderProductForViewing[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}
