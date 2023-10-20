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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPreview;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Returns preview data for a product or a list of product
 *
 * @todo add function for the list that should be used in the new product search API
 */
class ProductPreviewRepository
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageProviderInterface
     */
    private $productImageProvider;

    public function __construct(
        ProductRepository $productRepository,
        ProductImageProviderInterface $productImageProvider
    ) {
        $this->productRepository = $productRepository;
        $this->productImageProvider = $productImageProvider;
    }

    public function getPreview(ProductId $productId, LanguageId $languageId): ProductPreview
    {
        $shopId = $this->productRepository->getProductDefaultShopId($productId);
        $product = $this->productRepository->get($productId, $shopId);

        return new ProductPreview(
            $productId->getValue(),
            $product->name[$languageId->getValue()] ?? reset($product->name),
            $this->productImageProvider->getProductCoverUrl($productId, $shopId)
        );
    }
}
