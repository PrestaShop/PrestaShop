<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\RemoveAllAssociatedProductSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Removes all product suppliers for specified product without combinations
 *
 * @see RemoveAllAssociatedProductSuppliersHandlerInterface
 */
class RemoveAllAssociatedProductSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
