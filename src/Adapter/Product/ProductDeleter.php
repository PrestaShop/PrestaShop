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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Deletes products using legacy object model
 */
final class ProductDeleter extends AbstractObjectModelPersister implements ProductDeleterInterface
{
    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @param ProductProvider $productProvider
     */
    public function __construct(
        ProductProvider $productProvider
    ) {
        $this->productProvider = $productProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ProductId $productId): void
    {
        $product = $this->productProvider->get($productId);

        if (!$this->deleteObjectModel($product)) {
            throw new CannotDeleteProductException(sprintf('Failed to delete product #%d', $product->id));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $productIds): void
    {
        $failedIds = [];
        foreach ($productIds as $productId) {
            if (!$this->deleteObjectModel($this->productProvider->get($productId))) {
                $failedIds[] = $productId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteProductException(
            $failedIds,
            sprintf('Failed to delete following products: "%s"', implode(', ', $failedIds))
        );
    }
}
