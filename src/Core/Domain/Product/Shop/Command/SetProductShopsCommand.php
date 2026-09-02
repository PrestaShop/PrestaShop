<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SetProductShopsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopId
     */
    private $sourceShopId;

    /**
     * @var ShopId[]
     */
    private $shopIds;

    /**
     * @param int $productId the product for which the new shop association is being set
     * @param int $sourceShopId the source shop used as a reference for copy when a new shop association is being created
     * @param int[] $shopIds ids of all the associated shops for this product,
     *                       any shop which is not present in this list - will be unassociated,
     *                       any shop not yet associated - will induce a new shop association creation
     */
    public function __construct(
        int $productId,
        int $sourceShopId,
        array $shopIds
    ) {
        $this->assertShopsNotEmpty($shopIds);
        $this->assertSourceShopExistsInShops($sourceShopId, $shopIds);
        $this->productId = new ProductId($productId);
        $this->sourceShopId = new ShopId($sourceShopId);

        $this->shopIds = array_map(static function (int $shopId): ShopId {
            return new ShopId($shopId);
        }, $shopIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopId
     */
    public function getSourceShopId(): ShopId
    {
        return $this->sourceShopId;
    }

    /**
     * @return ShopId[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    /**
     * @param int $sourceShopId
     * @param int[] $shopIds
     */
    private function assertSourceShopExistsInShops(int $sourceShopId, array $shopIds): void
    {
        if (in_array($sourceShopId, $shopIds, true)) {
            return;
        }

        throw new InvalidProductShopAssociationException(
            'Source shop must be one of associated shops',
            InvalidProductShopAssociationException::SOURCE_SHOP_MISSING_IN_SHOP_ASSOCIATION
        );
    }

    /**
     * @param int[] $shopIds
     */
    private function assertShopsNotEmpty(array $shopIds): void
    {
        if (empty($shopIds)) {
            throw new InvalidProductShopAssociationException(
                sprintf(
                    'Empty shop association provided. Use %s command to delete product instead',
                    DeleteProductCommand::class
                ),
                InvalidProductShopAssociationException::EMPTY_SHOPS_ASSOCIATION
            );
        }
    }
}
