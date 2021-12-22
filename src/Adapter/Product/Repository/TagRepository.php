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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Tag;

/**
 * Accesses product Tag data source
 */
class TagRepository
{
    public function addTagsByLanguage(ProductId $productId, LocalizedTags $localizedTags): void
    {
        $productIdValue = $productId->getValue();
        $langIdValue = $localizedTags->getLanguageId()->getValue();

        try {
            // assign new tags to product
            if (!Tag::addTags($langIdValue, $productIdValue, $localizedTags->getTags())) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%d tags in lang #%d', $productIdValue, $langIdValue),
                    CannotUpdateProductException::FAILED_UPDATE_TAGS
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to add tags to product #%d', $productIdValue
            ));
        }
    }

    /**
     * @param ProductId $productId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function deleteAllTags(ProductId $productId): void
    {
        $productIdValue = $productId->getValue();

        try {
            if (!Tag::deleteTagsForProduct($productIdValue)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to delete all tags for product #%d', $productIdValue),
                    CannotUpdateProductException::FAILED_UPDATE_TAGS
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to delete product #%d tags', $productIdValue
            ));
        }
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function deleteTagsByLanguage(ProductId $productId, LanguageId $languageId): void
    {
        $productIdValue = $productId->getValue();
        $langIdValue = $languageId->getValue();

        try {
            if (!Tag::deleteProductTagsInLang($productIdValue, $langIdValue)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to delete product #%d previous tags in lang #%d', $productIdValue, $langIdValue),
                    CannotUpdateProductException::FAILED_UPDATE_TAGS
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to delete product #%d tags', $productIdValue
            ));
        }
    }
}
