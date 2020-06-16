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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;
use Product;
use Tag;

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

        $this->performUpdate($product, $command->getLocalizedTags());
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

        if (null !== $command->toShowPrice()) {
            $product->show_price = $command->toShowPrice();
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
    private function performUpdate(Product $product, ?array $localizedTags)
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

            if (null !== $localizedTags) {
                $this->updateTags($product, $localizedTags);
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
     * @param Product $product
     * @param array $localizedTags
     *
     * @throws CannotUpdateProductException
     * @throws PrestaShopException
     */
    private function updateTags(Product $product, array $localizedTags)
    {
        $productId = (int) $product->id;

        if (empty($localizedTags)) {
            $this->deleteAllProductTags($productId);
        }

        foreach ($localizedTags as $langId => $tags) {
            if (false === Tag::deleteTagsForProduct($productId, $langId)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to delete product #%s previous tags in lang #%s', $productId, $langId),
                    CannotUpdateProductException::FAILED_UPDATE_OPTIONS
                );
            }

            if (false === Tag::addTags($langId, $productId, $tags)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%s tags in lang #%s', $productId, $langId),
                    CannotUpdateProductException::FAILED_UPDATE_OPTIONS
                );
            }
        }
    }

    /**
     * @param int $productId
     *
     * @throws CannotUpdateProductException
     * @throws PrestaShopException
     */
    private function deleteAllProductTags(int $productId): void
    {
        if (false === Tag::deleteTagsForProduct($productId)) {
            throw new CannotUpdateProductException(
                sprintf('Failed to delete product #%s tags', $productId),
                CannotUpdateProductException::FAILED_UPDATE_OPTIONS
            );
        }
    }
}
