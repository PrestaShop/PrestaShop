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

use PrestaShop\PrestaShop\Adapter\Product\Repository\TagRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

/**
 * Methods related for product-tags update operations
 */
class ProductTagUpdater
{
    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param TagRepository $tagRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        TagRepository $tagRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->tagRepository = $tagRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * Removes previous tags & sets new list of tags for a product.
     *
     * @param Product $product
     * @param LocalizedTags[] $localizedTagsList
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function setProductTags(Product $product, array $localizedTagsList): void
    {
        $productId = new ProductId((int) $product->id);

        // We check if the values have changed, it represents an additional query to check but it's better than performing
        // an update of search indexes for nothing.
        if (!$this->hasModification($productId, $localizedTagsList)) {
            return;
        }

        // delete all tags for product if array is empty
        if (empty($localizedTagsList)) {
            $this->tagRepository->deleteAllTags($productId);
        } else {
            foreach ($localizedTagsList as $localizedTags) {
                // delete all this product tags for this lang
                $this->tagRepository->deleteTagsByLanguage($productId, $localizedTags->getLanguageId());

                // empty tags means to delete all previous tags, which is already done above.
                if ($localizedTags->isEmpty()) {
                    continue;
                }

                // assign new tags to product
                $this->tagRepository->addTagsByLanguage($productId, $localizedTags);
            }
        }

        // Since tags have been modified we need to update the indexation values (only for active products)
        if ($product->active) {
            $this->productIndexationUpdater->updateIndexation($product);
        }
    }

    /**
     * @param ProductId $productId
     * @param LocalizedTags[] $localizedTagsList
     *
     * @return bool
     */
    private function hasModification(ProductId $productId, array $localizedTagsList): bool
    {
        $localizedProductTags = $this->tagRepository->getLocalizedProductTags($productId);
        $currentTagLanguages = array_keys($localizedProductTags);
        $updateTagLanguages = array_map(static function (LocalizedTags $localizedTags): int {
            return $localizedTags->getLanguageId()->getValue();
        }, $localizedTagsList);

        if (array_diff($currentTagLanguages, $updateTagLanguages)) {
            return true;
        }

        foreach ($localizedTagsList as $localizedTags) {
            if (empty($localizedProductTags[$localizedTags->getLanguageId()->getValue()])) {
                return true;
            }

            $currentTags = $this->stringifyTags($localizedProductTags[$localizedTags->getLanguageId()->getValue()] ?? []);
            $updateTags = $this->stringifyTags($localizedTags->getTags());
            if ($currentTags !== $updateTags) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $tags
     *
     * @return string
     */
    private function stringifyTags(array $tags): string
    {
        asort($tags);

        return implode(';', $tags);
    }
}
