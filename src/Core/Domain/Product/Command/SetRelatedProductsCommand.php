<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use RuntimeException;

/**
 * Sets related products for product
 */
class SetRelatedProductsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductId[]
     */
    private $relatedProductIds;

    /**
     * @param int $productId
     * @param int[] $relatedProductIds
     */
    public function __construct(
        int $productId,
        array $relatedProductIds
    ) {
        $this->productId = new ProductId($productId);
        $this->setRelatedProductIds($relatedProductIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductId[]
     */
    public function getRelatedProductIds(): array
    {
        return $this->relatedProductIds;
    }

    /**
     * @param int[] $ids
     */
    private function setRelatedProductIds(array $ids): void
    {
        if (empty($ids)) {
            throw new RuntimeException(sprintf(
                'Empty array of related products provided in %s. To remove all related products use %s.',
                self::class,
                RemoveAllRelatedProductsCommand::class
            ));
        }

        foreach ($ids as $id) {
            $this->relatedProductIds[] = new ProductId($id);
        }
    }
}
