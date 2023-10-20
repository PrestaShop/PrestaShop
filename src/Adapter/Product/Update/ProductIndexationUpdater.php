<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
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
        try {
            $this->adaptShopContext($shopConstraint);
            if (!Search::indexation(false, $productId)) {
                throw new CannotUpdateProductException(
                    sprintf('Cannot update search indexes for product %d', $productId),
                    CannotUpdateProductException::FAILED_UPDATE_SEARCH_INDEXATION
                );
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

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     *
     * @throws CoreException
     */
    private function removeProductIndexes(int $productId, ShopConstraint $shopConstraint): void
    {
        try {
            $this->adaptShopContext($shopConstraint);
            Search::removeProductsSearchIndex([$productId]);
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

    private function adaptShopContext(ShopConstraint $shopConstraint): void
    {
        $this->contextStateManager->saveCurrentContext();
        if ($shopConstraint->getShopId()) {
            $this->contextStateManager->setShop(new Shop($shopConstraint->getShopId()->getValue()));
        } elseif ($shopConstraint->getShopGroupId()) {
            $this->contextStateManager->setShopContext(Shop::CONTEXT_GROUP, $shopConstraint->getShopGroupId()->getValue());
        } else {
            $this->contextStateManager->setShopContext(Shop::CONTEXT_ALL);
        }
    }
}
