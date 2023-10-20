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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetRelatedProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class GetRelatedProductsHandler implements GetRelatedProductsHandlerInterface
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

    /**
     * @param ProductRepository $productRepository
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImagePathFactory
     */
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
     * {@inheritdoc}
     */
    public function handle(GetRelatedProducts $query): array
    {
        $results = $this->productRepository->getRelatedProducts($query->getProductId(), $query->getLanguageId());
        $relatedProducts = [];

        foreach ($results as $result) {
            $productId = new ProductId((int) $result['id_product']);
            // related products are not multishop compatible,
            // so we just use default product shop to retrieve info required by multishop repositories
            $shopId = $this->productRepository->getProductDefaultShopId($productId);
            $imageId = $this->productImageRepository->getDefaultImageId($productId, $shopId);
            $imagePath = $imageId ?
                $this->productImagePathFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT) :
                $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT)
            ;

            $relatedProducts[] = new RelatedProduct(
                $productId->getValue(),
                $result['name'],
                $result['reference'],
                $imagePath
            );
        }

        return $relatedProducts;
    }
}
