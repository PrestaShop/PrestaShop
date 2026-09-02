<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Provides related products for given product
 */
class GetRelatedProducts
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param int $productId
     * @param int $languageId
     */
    public function __construct(
        int $productId,
        int $languageId
    ) {
        $this->productId = new ProductId($productId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }
}
