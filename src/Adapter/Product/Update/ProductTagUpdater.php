<?php
/*
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
     * @param TagRepository $tagRepository
     */
    public function __construct(
        TagRepository $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
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

        // delete all tags for product if array is empty
        if (empty($localizedTagsList)) {
            $this->tagRepository->deleteAllTags($productId);

            return;
        }

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
}
