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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class ProductStatusUpdater
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param ProductMultiShopRepository $productRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        ProductMultiShopRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * @param ProductId $productId
     * @param bool $newStatus
     * @param ShopConstraint $shopConstraint
     */
    public function updateStatus(ProductId $productId, bool $newStatus, ShopConstraint $shopConstraint): void
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        $initialStatus = (bool) $product->active;
        $product->active = $newStatus;
        $this->productRepository->partialUpdate(
            $product,
            ['active'],
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_STATUS
        );

        // We cannot easily check if status changed in multi-shop context, because product is loaded from a single shop
        // (it would end up checking one shop product and leaving all other shops unhandled)
        // So in multi-shop context we always reindex product
        if ($shopConstraint->forAllShops()) {
            $this->productIndexationUpdater->updateIndexation($product, $shopConstraint);

            return;
        }

        // In single shop context we check if status changed to make sure we need to reindex product
        // because reindexing is an expensive operation performance-wise
        if ($initialStatus !== $newStatus) {
            $this->productIndexationUpdater->updateIndexation($product, $shopConstraint);
        }
    }
}
