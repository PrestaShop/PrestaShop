<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductIsEnabled;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductIsEnabledHandlerInterface;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetProductIsEnabledHandler implements GetProductIsEnabledHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetProductIsEnabled $query)
    {
        $productId = $query->getProductId()->getValue();
        $product = new Product($productId);

        if ($product->id !== $productId) {
            throw new ProductNotFoundException(sprintf('Product with id "%d" was not found.', $productId));
        }

        return (bool) $product->active;
    }
}
