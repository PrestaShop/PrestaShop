<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use Context;
use Hook;
use Pack;
use PrestaShop\PrestaShop\Adapter\CacheManager;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Product\PackItemsManager;
use PrestaShop\PrestaShop\Adapter\StockManager as StockProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use Shop;
use ShopGroup;
use StockAvailable;
use Validate;

/**
 * Single entry point to change the available quantity of a product or a combination.
 *
 * Its methods only take identifiers, the legacy objects stay behind them.
 * The methods marked internal exist for the deprecated legacy API and will be removed with it.
 */
final class StockQuantityUpdater
{
    public function __construct(
        private readonly StockAvailableQuantityUpdater $quantityWriter,
        private readonly StockProvider $stockProvider,
        private readonly PackItemsManager $packItemsManager,
        private readonly CacheManager $cacheManager,
        private readonly HookManager $hookManager,
        private readonly ConfigurationInterface $configuration,
        private readonly StockManager $stockManager,
    ) {
    }

    /**
     * Puts units back in stock, a return for instance. The products of a pack follow.
     *
     * @param int|null $shopId null for the shop of the context
     *
     * @return bool false when the product does not exist
     */
    public function increaseQuantity(
        int $productId,
        int $combinationId,
        int $quantity,
        ?int $shopId = null,
        bool $recordMovement = false,
        ?int $orderId = null,
        ?int $movementReasonId = null,
    ): bool {
        return $this->changeQuantity($productId, $combinationId, $quantity, $shopId, $recordMovement, $orderId, $movementReasonId);
    }

    /**
     * Takes units out of stock, a sale for instance. The products of a pack and the packs containing the product follow.
     *
     * @param int|null $shopId null for the shop of the context
     *
     * @return bool false when the product does not exist
     */
    public function decreaseQuantity(
        int $productId,
        int $combinationId,
        int $quantity,
        ?int $shopId = null,
        bool $recordMovement = false,
        ?int $orderId = null,
        ?int $movementReasonId = null,
    ): bool {
        return $this->changeQuantity($productId, $combinationId, -$quantity, $shopId, $recordMovement, $orderId, $movementReasonId);
    }

    /**
     * Edition of one stock by an employee: packs are left untouched.
     *
     * @internal for the back-office stock updaters
     *
     * @param string[] $otherProperties properties already set on the stock, stored with the quantity
     * @param int|null $hookShopId shop given to actionUpdateQuantity when it is not $shopId
     *
     * @return bool false when the stock could not be written
     */
    public function editStock(
        StockAvailable $stockAvailable,
        StockModification $modification,
        int $shopId,
        int $movementReasonId,
        array $otherProperties = [],
        ?int $hookShopId = null,
    ): bool {
        $previousQuantity = (int) $stockAvailable->quantity;
        // The shop decides which product total is recomputed for a combination
        $stockAvailable->id_shop_list = [$shopId];

        $deltaQuantity = $modification->getDeltaQuantity();
        $isUpdated = null !== $deltaQuantity
            ? $this->quantityWriter->applyDelta($stockAvailable, $deltaQuantity, $otherProperties)
            : $this->quantityWriter->setQuantity($stockAvailable, $modification->getFixedQuantity(), $otherProperties);
        if (!$isUpdated) {
            return false;
        }

        $deltaQuantity ??= $modification->getFixedQuantity() - $previousQuantity;
        $productId = (int) $stockAvailable->id_product;
        $combinationId = (int) $stockAvailable->id_product_attribute;
        $this->stockManager->saveMovement($productId, $combinationId, $deltaQuantity, [
            'id_stock_mvt_reason' => $movementReasonId,
            'id_shop' => $shopId,
        ]);
        $this->dispatchQuantityUpdate($productId, $combinationId, (int) $stockAvailable->quantity, $deltaQuantity, $hookShopId ?? $shopId);
        $this->cleanQuantityCache($productId);

        return true;
    }

    private function changeQuantity(
        int $productId,
        int $combinationId,
        int $deltaQuantity,
        ?int $shopId,
        bool $recordMovement,
        ?int $orderId,
        ?int $movementReasonId,
    ): bool {
        if ($productId <= 0) {
            return false;
        }
        $product = new Product($productId);
        if (!Validate::isLoadedObject($product)) {
            return false;
        }

        $this->applyDeltaToProduct($product, $combinationId, $deltaQuantity, $shopId, $recordMovement, array_filter([
            'id_order' => $orderId,
            'id_stock_mvt_reason' => $movementReasonId,
        ]));

        return true;
    }

    /**
     * Sets the quantity, creating the stock when it does not exist yet. Packs are left untouched.
     *
     * @param int|null $shopId null for the shop of the context
     *
     * @return bool false when the quantity could not be stored
     */
    public function setQuantity(
        int $productId,
        int $combinationId,
        int $quantity,
        ?int $shopId = null,
        bool $recordMovement = true,
    ): bool {
        if ($productId <= 0) {
            return false;
        }
        if (null === $shopId && Shop::getContext() != Shop::CONTEXT_GROUP) {
            $shopId = (int) Context::getContext()->shop->id;
        }

        $deltaQuantity = null;
        $stockId = (int) StockAvailable::getStockAvailableIdByProductId($productId, $combinationId, $shopId);
        if ($stockId) {
            $stockAvailable = new StockAvailable($stockId);
            $deltaQuantity = $quantity - (int) $stockAvailable->quantity;
            $isStored = $this->quantityWriter->setQuantity($stockAvailable, $quantity);
        } else {
            $stockAvailable = $this->createStock($productId, $combinationId, $quantity, $shopId);
            $isStored = (bool) $stockAvailable->id;
        }

        $movedQuantity = $deltaQuantity ?? $quantity;
        if ($recordMovement && 0 !== $movedQuantity) {
            $this->stockManager->saveMovement($productId, $combinationId, $movedQuantity);
        }

        // Dispatched directly, as the legacy setQuantity() always did
        Hook::exec('actionUpdateQuantity', [
            'id_product' => $productId,
            'id_product_attribute' => $combinationId,
            'quantity' => (int) $stockAvailable->quantity,
            'delta_quantity' => $deltaQuantity,
            'id_shop' => $shopId,
        ]);
        $this->cleanQuantityCache($productId);

        return $isStored;
    }

    /**
     * @internal for the deprecated StockManager::updateQuantity()
     *
     * @param array<string, mixed> $movementParams
     */
    public function applyDeltaToProduct(
        Product $product,
        ?int $combinationId,
        int $deltaQuantity,
        ?int $shopId = null,
        bool $recordMovement = false,
        array $movementParams = [],
    ): void {
        $stockAvailable = $this->stockProvider->getStockAvailableByProduct($product, $combinationId, $shopId);

        if ($this->packItemsManager->isPack($product)) {
            $this->applyDeltaToPack($product, $stockAvailable, $deltaQuantity, $shopId);
        } else {
            $this->quantityWriter->applyDelta($stockAvailable, $deltaQuantity);

            if ($deltaQuantity < 0 && $this->packItemsManager->isPacked($product, $combinationId)) {
                $this->capPacksContaining($product, $combinationId, $stockAvailable, $shopId);
            }
        }

        if ($recordMovement && 0 !== $deltaQuantity) {
            $this->stockManager->saveMovement($product->id, $combinationId, $deltaQuantity, $movementParams);
        }

        $this->dispatchQuantityUpdate((int) $product->id, $combinationId, (int) $stockAvailable->quantity, $deltaQuantity, $shopId);
        $this->stockManager->sendLowStockAlertIfNeeded($product, $combinationId, $stockAvailable->quantity);
        $this->cleanQuantityCache((int) $product->id);
    }

    /**
     * @internal for the deprecated StockManager::updatePackQuantity()
     */
    public function applyDeltaToPack(Product $pack, StockAvailable $packStock, int $deltaQuantity, ?int $shopId = null): void
    {
        $stockType = $this->getStockType((int) $pack->pack_stock_type);

        if ($stockType !== Pack::STOCK_TYPE_PACK_ONLY) {
            foreach ($this->packItemsManager->getPackItems($pack) as $packItem) {
                $itemStock = $this->stockProvider->getStockAvailableByProduct($packItem, $packItem->id_pack_product_attribute, $shopId);
                $this->quantityWriter->applyDelta($itemStock, (int) ($deltaQuantity * $packItem->pack_quantity));
                $this->cleanQuantityCache((int) $packItem->id);
            }
        }

        if (in_array($stockType, [Pack::STOCK_TYPE_PACK_ONLY, Pack::STOCK_TYPE_PACK_BOTH], true)) {
            $this->quantityWriter->applyDelta($packStock, $deltaQuantity);
        } else {
            // The pack stock is not stored, but the caller still reads the quantity it would have
            $packStock->quantity = (int) $packStock->quantity + $deltaQuantity;
        }
    }

    /**
     * A pack decrementing both stocks cannot hold more units than its products allow.
     *
     * @internal for the deprecated StockManager::updatePacksQuantityContainingProduct()
     */
    public function capPacksContaining(Product $product, ?int $combinationId, StockAvailable $productStock, ?int $shopId = null): void
    {
        foreach ($this->packItemsManager->getPacksContainingItem($product, $combinationId) as $pack) {
            if ($this->getStockType((int) $pack->pack_stock_type) !== Pack::STOCK_TYPE_PACK_BOTH) {
                continue;
            }

            $maxPackQuantity = max(0, (int) floor($productStock->quantity / $pack->pack_item_quantity));
            $packStock = $this->stockProvider->getStockAvailableByProduct($pack, null, $shopId);
            if ($packStock->quantity > $maxPackQuantity) {
                $this->quantityWriter->setQuantity($packStock, $maxPackQuantity);
                $this->cleanQuantityCache((int) $pack->id);
            }
        }
    }

    /**
     * A pack left on the shop default follows the configuration.
     */
    private function getStockType(int $stockType): int
    {
        return Pack::STOCK_TYPE_DEFAULT === $stockType
            ? (int) $this->configuration->get('PS_PACK_STOCK_TYPE')
            : $stockType;
    }

    private function createStock(int $productId, int $combinationId, int $quantity, ?int $shopId): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->out_of_stock = (int) StockAvailable::outOfStock($productId, $shopId);
        $stockAvailable->id_product = $productId;
        $stockAvailable->id_product_attribute = $combinationId;
        $stockAvailable->quantity = $quantity;

        $shopGroup = null === $shopId
            ? Shop::getContextShopGroup()
            : new ShopGroup((int) Shop::getGroupFromShop($shopId));
        if ($shopGroup->share_stock) {
            $stockAvailable->id_shop = 0;
            $stockAvailable->id_shop_group = (int) $shopGroup->id;
        } else {
            $stockAvailable->id_shop = (int) $shopId;
            $stockAvailable->id_shop_group = 0;
        }
        $stockAvailable->add();

        return $stockAvailable;
    }

    private function dispatchQuantityUpdate(int $productId, ?int $combinationId, int $quantity, ?int $deltaQuantity, ?int $shopId): void
    {
        $this->hookManager->exec('actionUpdateQuantity', [
            'id_product' => $productId,
            'id_product_attribute' => $combinationId,
            'quantity' => $quantity,
            'delta_quantity' => $deltaQuantity,
            'id_shop' => $shopId,
        ]);
    }

    private function cleanQuantityCache(int $productId): void
    {
        $this->cacheManager->clean('StockAvailable::getQuantityAvailableByProduct_' . $productId . '*');
    }
}
