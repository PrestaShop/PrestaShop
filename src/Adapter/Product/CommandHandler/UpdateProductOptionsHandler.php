<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;
use Product;
use Tag;
use Validate;

/**
 * Handles UpdateProductOptionsCommand using legacy object models
 */
final class UpdateProductOptionsHandler extends AbstractProductHandler implements UpdateProductOptionsHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductOptionsCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $this->fillUpdatableFieldsWithCommandData($product, $command);
        $product->setFieldsToUpdate($this->fieldsToUpdate);

        $this->performUpdate($product);

        // don't do anything with tags if its null. (It means it is a partial update and tags aren't changed)
        if (null !== $command->getLocalizedTags()) {
            $this->updateTags($product, $command->getLocalizedTags());
        }
    }

    /**
     * @param Product $product
     * @param UpdateProductOptionsCommand $command
     */
    private function fillUpdatableFieldsWithCommandData(Product $product, UpdateProductOptionsCommand $command): void
    {
        if (null !== $command->getVisibility()) {
            $product->visibility = $command->getVisibility()->getValue();
            $this->fieldsToUpdate['visibility'] = true;
        }

        if (null !== $command->isAvailableForOrder()) {
            $product->available_for_order = $command->isAvailableForOrder();
            $this->fieldsToUpdate['available_for_order'] = true;
        }

        if (null !== $command->isOnlineOnly()) {
            $product->online_only = $command->isOnlineOnly();
            $this->fieldsToUpdate['online_only'] = true;
        }

        if (null !== $command->showPrice()) {
            $product->show_price = $command->showPrice();
            $this->fieldsToUpdate['show_price'] = true;
        }

        if (null !== $command->getCondition()) {
            $product->condition = $command->getCondition()->getValue();
            $this->fieldsToUpdate['condition'] = true;
        }

        if (null !== $command->getEan13()) {
            $product->ean13 = $command->getEan13()->getValue();
            $this->fieldsToUpdate['ean13'] = true;
        }

        if (null !== $command->getIsbn()) {
            $product->isbn = $command->getIsbn()->getValue();
            $this->fieldsToUpdate['isbn'] = true;
        }

        if (null !== $command->getMpn()) {
            $product->mpn = $command->getMpn()->getValue();
            $this->fieldsToUpdate['mpn'] = true;
        }

        if (null !== $command->getReference()) {
            $product->reference = $command->getReference()->getValue();
            $this->fieldsToUpdate['reference'] = true;
        }

        if (null !== $command->getUpc()) {
            $product->upc = $command->getUpc()->getValue();
            $this->fieldsToUpdate['upc'] = true;
        }
    }

    /**
     * @param Product $product
     * @param array|null $localizedTags
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function performUpdate(Product $product)
    {
        try {
            if (false === $product->update()) {
                throw new CannotUpdateProductException(
                    sprintf(
                        'Failed to update product #%s options',
                        $product->id
                    ),
                    CannotUpdateProductException::FAILED_UPDATE_OPTIONS
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf(
                    'Error occurred during product #%s options update',
                    $product->id
                ),
                0,
                $e
            );
        }
    }

    /**
     * Due to this method it is possible to partially update tags in separate languages.
     *
     * If all localizedTags array is empty, all previous tags will be deleted.
     * If tags in some language are null, it will be skipped.
     * If tags in some language has any values, all previous tags will be deleted and new ones inserted instead
     * If tags in some language are empty, then all previous tags will be deleted and none inserted.
     *
     * @param Product $product
     * @param array $localizedTags
     *
     * @throws CannotUpdateProductException
     * @throws PrestaShopException
     */
    private function updateTags(Product $product, array $localizedTags)
    {
        $productId = (int) $product->id;

        // delete all tags for product if array is empty
        if (empty($localizedTags)) {
            $this->deleteAllTagsForProduct($productId);

            return;
        }

        foreach ($localizedTags as $langId => $tags) {
            // validate each tag and remove empty values
            $tags = $this->validateTags($tags, $langId);

            // delete all this product tags for this lang
            if (false === Tag::deleteTagsForProduct($productId, $langId)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to delete product #%s previous tags in lang #%s', $productId, $langId),
                    CannotUpdateProductException::FAILED_UPDATE_OPTIONS
                );
            }

            // empty tags means to delete all previous tags, which is already done above.
            if (empty($tags)) {
                continue;
            }

            // assign new tags to product
            if (false === Tag::addTags($langId, $productId, $tags)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%s tags in lang #%s', $productId, $langId),
                    CannotUpdateProductException::FAILED_UPDATE_OPTIONS
                );
            }
        }
    }

    /**
     * Validate each tag in provided language and rebuild the array removing empty values.
     *
     * @param array $tags
     * @param int $langId
     *
     * @return array
     *
     * @throws ProductConstraintException
     */
    private function validateTags(array $tags, int $langId): array
    {
        $validTags = [];

        foreach ($tags as $key => $tag) {
            //skip empty value
            if (empty($tag)) {
                continue;
            }

            //validate tag
            if (false === Validate::isGenericName($tag)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Invalid product tag "%s" in language with id "%s"',
                        $tag,
                        $langId
                    ),
                    ProductConstraintException::INVALID_TAG
                );
            }

            $validTags[] = $tag;
        }

        return $validTags;
    }

    /**
     * @param int $productId
     *
     * @throws CannotUpdateProductException
     * @throws PrestaShopException
     */
    private function deleteAllTagsForProduct(int $productId): void
    {
        if (false === Tag::deleteTagsForProduct($productId)) {
            throw new CannotUpdateProductException(
                sprintf('Failed to delete all tags for product #%s', $productId),
                CannotUpdateProductException::FAILED_UPDATE_OPTIONS
            );
        }
    }
}
