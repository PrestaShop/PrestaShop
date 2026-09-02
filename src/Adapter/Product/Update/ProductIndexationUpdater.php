<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;
use Search;
use Shop;

/**
 * Updates product indexation
 */
class ProductIndexationUpdater
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var bool
     */
    private $isSearchIndexationOn;

    public function __construct(
        ContextStateManager $contextStateManager,
        bool $isSearchIndexationOn
    ) {
        $this->contextStateManager = $contextStateManager;
        $this->isSearchIndexationOn = $isSearchIndexationOn;
    }

    /**
     * Checks if one of the updated fields is used for the indexation, if one of them is
     * then a new indexation is needed.
     *
     * @param array $updatedFields
     *
     * @return bool
     */
    public function isIndexationNeeded(array $updatedFields): bool
    {
        $indexedFields = [
            'active',
            'visibility',
            'name',
            'description',
            'description_short',
            'reference',
            'isbn',
            'upc',
            'ean13',
            'mpn',
        ];

        foreach ($updatedFields as $langFieldName => $regularFieldName) {
            if (in_array($langFieldName, $indexedFields) || in_array($regularFieldName, $indexedFields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function isVisibleOnSearch(Product $product): bool
    {
        return in_array(
            $product->visibility,
            [ProductVisibility::VISIBLE_EVERYWHERE, ProductVisibility::VISIBLE_IN_SEARCH]
        ) && $product->active;
    }

    /**
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function updateIndexation(Product $product, ShopConstraint $shopConstraint): void
    {
        if (!$this->isSearchIndexationOn) {
            return;
        }

        if ($this->isVisibleOnSearch($product)) {
            $this->updateProductIndexes((int) $product->id, $shopConstraint);
        } else {
            $this->removeProductIndexes((int) $product->id, $shopConstraint);
        }
    }

    /**
     * @param int $productId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    private function updateProductIndexes(int $productId, ShopConstraint $shopConstraint): void
    {
        $this->contextStateManager->saveCurrentContext();
        try {
            // If a specific list is provided we update them one by one
            if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
                foreach ($shopConstraint->getShopIds() as $shopId) {
                    $this->updateProductIndexesByShopConstraint($productId, ShopConstraint::shop($shopId->getValue()));
                }
            } else {
                // If not the other types of ShopConstraint are handled by this method
                $this->updateProductIndexesByShopConstraint($productId, $shopConstraint);
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred while updating search indexes for product %d', $productId),
                0,
                $e
            );
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    private function updateProductIndexesByShopConstraint(int $productId, ShopConstraint $shopConstraint): void
    {
        $this->adaptShopContext($shopConstraint);
        if (!Search::indexation(false, $productId)) {
            throw new CannotUpdateProductException(
                sprintf('Cannot update search indexes for product %d', $productId),
                CannotUpdateProductException::FAILED_UPDATE_SEARCH_INDEXATION
            );
        }
    }

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     *
     * @throws CoreException
     */
    private function removeProductIndexes(int $productId, ShopConstraint $shopConstraint): void
    {
        $this->contextStateManager->saveCurrentContext();
        try {
            // If a specific list is provided we update them one by one
            if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
                foreach ($shopConstraint->getShopIds() as $shopId) {
                    $this->removeProductIndexesByShopConstraint($productId, ShopConstraint::shop($shopId->getValue()));
                }
            } else {
                // If not the other types of ShopConstraint are handled by this method
                $this->removeProductIndexesByShopConstraint($productId, $shopConstraint);
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred while removing search indexes for product %d', $productId),
                0,
                $e
            );
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    private function removeProductIndexesByShopConstraint(int $productId, ShopConstraint $shopConstraint): void
    {
        $this->adaptShopContext($shopConstraint);
        Search::removeProductsSearchIndex([$productId]);
    }

    private function adaptShopContext(ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopId()) {
            $this->contextStateManager->setShop(new Shop($shopConstraint->getShopId()->getValue()));
        } elseif ($shopConstraint->getShopGroupId()) {
            $this->contextStateManager->setShopContext(Shop::CONTEXT_GROUP, $shopConstraint->getShopGroupId()->getValue());
        } else {
            $this->contextStateManager->setShopContext(Shop::CONTEXT_ALL);
        }
    }
}
