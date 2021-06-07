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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductsForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductsForListingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class GetProductsForListingHandler implements GetProductsForListingHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    public function __construct(
        ProductRepository $productRepository,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetProductsForListing $query): array
    {
        $productPreviews = [];
        foreach ($query->getProductIds() as $productId) {
            $productPreviews[] = $this->formatProductPreview($productId, $query->getLanguageId());
        }

        return $productPreviews;
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     *
     * @return ProductForListing
     */
    private function formatProductPreview(ProductId $productId, LanguageId $languageId): ProductForListing
    {
        $product = $this->productRepository->get($productId);
        $name = $product->name[$languageId->getValue()] ?? reset($product->name);
        $imageId = $this->productImageRepository->getDefaultImageId($productId);

        return new ProductForListing(
            $productId->getValue(),
            $name,
            $this->productImagePathFactory->getPath($imageId)
        );
    }
}
