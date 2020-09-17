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

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

/**
 * Deletes products using legacy object model
 */
final class ProductDeleter implements ProductDeleterInterface
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

        if (!$this->deleteProduct($product)) {
            throw new CannotDeleteProductException(
                sprintf('Failed to delete product #%d', $product->id),
                CannotDeleteProductException::FAILED_DELETE
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $productIds): void
    {
        foreach ($productIds as $productId) {
            if (!$this->deleteProduct($this->productProvider->get($productId))) {
                throw new CannotDeleteProductException(
                    sprintf('Failed to delete product #%d', $productId->getValue()),
                    CannotDeleteProductException::FAILED_BULK_DELETE
                );
            }
        }
    }

    /**
     * @param Product $product
     *
     * @return bool
     *
     * @throws CoreException
     */
    private function deleteProduct(Product $product): bool
    {
        try {
            return $product->delete();
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to delete product #%d', $product->id),
                0,
                $e
            );
        }
    }
}
