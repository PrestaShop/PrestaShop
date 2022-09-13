<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class ProductStatusUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    public function updateStatus(ProductId $productId, bool $newStatus): void
    {
        $product = $this->productRepository->get($productId);
        $initialState = (bool) $product->active;
        $product->active = $newStatus;
        $this->productRepository->partialUpdate(
            $product,
            ['active'],
            CannotUpdateProductException::FAILED_UPDATE_STATUS
        );

        // If status changed we need to update its indexes (we check if it is necessary because index build can be
        // an expensive operation).
        if ($initialState !== $newStatus) {
            $this->productIndexationUpdater->updateIndexation($product);
        }
    }
}
