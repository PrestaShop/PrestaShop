<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Represents product image data with only the essential information needed for bulk thumbnail operations.
 */
class ProductImageForThumbnailGeneration
{
    public function __construct(
        private readonly ImageId $imageId,
        private readonly ProductId $productId
    ) {
    }

    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
