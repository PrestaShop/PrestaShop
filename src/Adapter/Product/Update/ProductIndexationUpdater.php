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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;
use Search;

/**
 * Updates product indexation
 */
class ProductIndexationUpdater
{
    /**
     * @var bool
     */
    private $isSearchIndexationOn;

    /**
     * @param bool $isSearchIndexationOn
     */
    public function __construct(bool $isSearchIndexationOn)
    {
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
    public function updateIndexation(Product $product): void
    {
        if (!$this->isSearchIndexationOn) {
            return;
        }

        if ($this->isVisibleOnSearch($product)) {
            $this->updateProductIndexes((int) $product->id);
        } else {
            $this->removeProductIndexes((int) $product->id);
        }
    }

    /**
     * @param int $productId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    private function updateProductIndexes(int $productId): void
    {
        try {
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
        }
    }

    /**
     * @param int $productId
     *
     * @throws CoreException
     */
    private function removeProductIndexes(int $productId): void
    {
        try {
            Search::removeProductsSearchIndex([$productId]);
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred while removing search indexes for product %d', $productId),
                0,
                $e
            );
        }
    }
}
