<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotUpdateStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use StockAvailable;

/**
 * Updates settings related to Product stock
 */
class ProductStockUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    public function __construct(
        ProductRepository $productRepository,
        StockAvailableRepository $stockAvailableRepository,
        private readonly MovementReasonRepository $movementReasonRepository,
        ShopConfigurationInterface $configuration,
        private readonly StockQuantityUpdater $stockQuantityUpdater,
    ) {
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->configuration = $configuration;
    }

    /**
     * @param ProductId $productId
     * @param ProductStockProperties $properties
     */
    public function update(ProductId $productId, ProductStockProperties $properties, ShopConstraint $shopConstraint): void
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        // Use the shop matching the Product instance (either the specified ShopId from constraint or the Product default shop)
        $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, new ShopId($product->getShopId()));

        $productUpdates = $this->fillUpdatableProperties($product, $stockAvailable, $properties);
        if (!empty($productUpdates)) {
            $this->productRepository->partialUpdate(
                $product,
                $productUpdates,
                $shopConstraint,
                CannotUpdateProductException::FAILED_UPDATE_STOCK
            );
        }

        $this->updateStockByShopConstraint($stockAvailable, $properties, $shopConstraint);
    }

    /**
     * Resets product stock to zero, both Product and associated StockAvailable are reset, and a stock movement linked to
     * the employee from context is generated.
     *
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    public function resetStock(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->forAllShops()) {
            $shopIds = $this->productRepository->getAssociatedShopIds($productId);
        } elseif ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            $shopIds = $shopConstraint->getShopIds();
        } else {
            $shopIds = [$shopConstraint->getShopId()];
        }

        foreach ($shopIds as $shopId) {
            $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, $shopId);
            if ((int) $stockAvailable->quantity === 0) {
                continue;
            }

            // Update product
            $shopConstraint = ShopConstraint::shop($shopId->getValue());
            $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
            $product->quantity = 0;
            $this->productRepository->partialUpdate(
                $product,
                ['quantity'],
                $shopConstraint,
                CannotUpdateProductException::FAILED_UPDATE_STOCK
            );

            // Update stock, the movement is related to the employee
            $this->editQuantity($stockAvailable, StockModification::buildFixedQuantity(0), $shopId->getValue());
            $fallbackShopId = $this->stockAvailableRepository->getFallbackShopId($stockAvailable);

            // Update reserved and physical quantity for this stock
            $shopConstraint = ShopConstraint::shop($fallbackShopId->getValue());
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, $shopConstraint)),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, $shopConstraint))
            );
        }
    }

    /**
     * @param Product $product
     * @param ProductStockProperties $properties
     *
     * @return string[]|array<string, int[]>
     */
    private function fillUpdatableProperties(
        Product $product,
        StockAvailable $stockAvailable,
        ProductStockProperties $properties
    ): array {
        $updatableProperties = [];

        if (null !== $properties->getLocation()) {
            $product->location = $properties->getLocation();
            $updatableProperties[] = 'location';
        }
        if (null !== $properties->getOutOfStockType()) {
            $product->out_of_stock = $properties->getOutOfStockType()->getValue();
            $updatableProperties[] = 'out_of_stock';
        }
        if (null !== $properties->getStockModification()) {
            $product->quantity = $properties->getStockModification()->getDeltaQuantity() !== null ?
                $stockAvailable->quantity + $properties->getStockModification()->getDeltaQuantity() :
                $properties->getStockModification()->getFixedQuantity()
            ;
            $updatableProperties[] = 'quantity';
        }

        return $updatableProperties;
    }

    private function updateStockByShopConstraint(StockAvailable $stockAvailable, ProductStockProperties $properties, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->forAllShops()) {
            // Since each stock has a distinct ID we can't use the ObjectModel multi shop feature based on id_shop_list,
            // so we manually loop to update each associated stocks
            $stockIds = $this->stockAvailableRepository->getAllShopsStockIds(
                new ProductId((int) $stockAvailable->id_product),
                (int) $stockAvailable->id_product_attribute === NoCombinationId::NO_COMBINATION_ID ? new NoCombinationId() : new CombinationId((int) $stockAvailable->id_product_attribute)
            );

            foreach ($stockIds as $stockId) {
                $shopStockAvailable = $this->stockAvailableRepository->get($stockId);
                $this->updateStockAvailable($shopStockAvailable, $properties);
            }
        } elseif ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            // For specific list of shops, we get the appropriate stock for each shop and update it
            $productId = new ProductId((int) $stockAvailable->id_product);
            foreach ($shopConstraint->getShopIds() as $shopId) {
                $shopStockAvailable = $this->stockAvailableRepository->getForProduct($productId, $shopId);
                $this->updateStockAvailable($shopStockAvailable, $properties);
            }
        } else {
            $this->updateStockAvailable($stockAvailable, $properties);
        }
    }

    private function updateStockAvailable(StockAvailable $stockAvailable, ProductStockProperties $properties)
    {
        $updatedProperties = [];

        if (null !== $properties->getOutOfStockType()) {
            $stockAvailable->out_of_stock = $properties->getOutOfStockType()->getValue();
            $updatedProperties[] = 'out_of_stock';
        }
        if (null !== $properties->getLocation()) {
            $stockAvailable->location = $properties->getLocation();
            $updatedProperties[] = 'location';
        }

        if (!$updatedProperties && !$properties->getStockModification()) {
            return;
        }

        $fallbackShopId = $this->stockAvailableRepository->getFallbackShopId($stockAvailable);
        if (!$properties->getStockModification()) {
            $this->stockAvailableRepository->updateProperties($stockAvailable, $updatedProperties, $fallbackShopId);
        } else {
            $this->editQuantity($stockAvailable, $properties->getStockModification(), $fallbackShopId->getValue(), $updatedProperties);

            // Update reserved and physical quantity for this stock
            $shopConstraint = ShopConstraint::shop($fallbackShopId->getValue());
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, $shopConstraint)),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, $shopConstraint))
            );
        }
    }

    /**
     * @param string[] $otherProperties properties already set on the stock, stored with the quantity
     */
    private function editQuantity(
        StockAvailable $stockAvailable,
        StockModification $stockModification,
        int $shopId,
        array $otherProperties = [],
    ): void {
        $previousQuantity = (int) $stockAvailable->quantity;
        $deltaQuantity = $stockModification->getDeltaQuantity() ?: $stockModification->getFixedQuantity() - $previousQuantity;
        $this->stockAvailableRepository->validateQuantity($stockAvailable, $previousQuantity + $deltaQuantity);
        $movementReasonId = $this->movementReasonRepository->getEmployeeEditionReasonId($deltaQuantity > 0);

        $isUpdated = $this->stockQuantityUpdater->editStock(
            $stockAvailable,
            $stockModification,
            $shopId,
            $movementReasonId->getValue(),
            $otherProperties
        );
        if (!$isUpdated) {
            throw new CannotUpdateStockAvailableException(sprintf('Failed to update quantity of stock #%d', $stockAvailable->id));
        }
    }
}
