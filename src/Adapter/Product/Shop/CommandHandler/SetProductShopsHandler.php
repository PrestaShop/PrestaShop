<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\ProductDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductShopUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\CommandHandler\SetProductShopsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsCommandHandler]
class SetProductShopsHandler implements SetProductShopsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductDeleter
     */
    private $productDeleter;

    /**
     * @var ProductShopUpdater
     */
    private $productShopUpdater;

    public function __construct(
        ProductRepository $productRepository,
        ProductDeleter $productDeleter,
        ProductShopUpdater $productShopUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productDeleter = $productDeleter;
        $this->productShopUpdater = $productShopUpdater;
    }

    public function handle(SetProductShopsCommand $command): void
    {
        $productId = $command->getProductId();
        $sourceShopId = $command->getSourceShopId();
        $selectedShopIds = $command->getShopIds();
        $initialShopIds = $this->productRepository->getAssociatedShopIds($productId);

        $this->assertSourceShopIsAlreadyAssociated($sourceShopId, $initialShopIds);

        $shopsToCopy = $this->findDifferentShopIds($selectedShopIds, $initialShopIds, $sourceShopId);
        $shopsToRemove = $this->findDifferentShopIds($initialShopIds, $selectedShopIds, $sourceShopId);

        // Remove non associated shops
        $this->productDeleter->deleteFromShops($productId, $shopsToRemove);

        // Copy data from source targets
        foreach ($shopsToCopy as $targetShopId) {
            $this->productShopUpdater->copyToShop(
                $productId,
                $sourceShopId,
                $targetShopId
            );
        }
    }

    /**
     * @param ShopId $sourceShopId
     * @param ShopId[] $initialShopIds
     */
    private function assertSourceShopIsAlreadyAssociated(ShopId $sourceShopId, array $initialShopIds): void
    {
        if ($this->shopInArray($sourceShopId, $initialShopIds)) {
            return;
        }

        throw new InvalidProductShopAssociationException(
            sprintf(
                'Source shopId must be one of current product shops. Could not find %d in the associated shops',
                $sourceShopId->getValue()
            ),
            InvalidProductShopAssociationException::SOURCE_SHOP_NOT_ASSOCIATED
        );
    }

    /**
     * Returns ids from $searchableShopIds array that are not present in $shopIds array.
     * The $shopToIgnore id is ignored and is never returned
     *
     * @param ShopId[] $searchableShopIds
     * @param ShopId[] $shopIds
     * @param ShopId $shopToIgnore
     *
     * @return ShopId[]
     */
    private function findDifferentShopIds(array $searchableShopIds, array $shopIds, ShopId $shopToIgnore): array
    {
        $differentShopIds = [];
        foreach ($searchableShopIds as $searchableShopId) {
            if (
                $searchableShopId->getValue() === $shopToIgnore->getValue()
                || $this->shopInArray($searchableShopId, $shopIds)
            ) {
                continue;
            }

            $differentShopIds[] = $searchableShopId;
        }

        return $differentShopIds;
    }

    /**
     * @param ShopId $searchableShopId
     * @param ShopId[] $shopIds
     *
     * @return bool
     */
    private function shopInArray(ShopId $searchableShopId, array $shopIds): bool
    {
        foreach ($shopIds as $shopId) {
            if ($shopId->getValue() === $searchableShopId->getValue()) {
                return true;
            }
        }

        return false;
    }
}
