<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop;

class ShopProductImages
{
    /**
     * @var ShopImageAssociationCollection
     */
    private $productImageCollection;

    /**
     * @var int
     */
    private $shopId;

    public function __construct(int $shopId, ShopImageAssociationCollection $productImageCollection)
    {
        $this->shopId = $shopId;
        $this->productImageCollection = $productImageCollection;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getProductImages(): ShopImageAssociationCollection
    {
        return $this->productImageCollection;
    }
}
