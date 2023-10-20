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

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class ProductImageProvider implements ProductImageProviderInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    public function __construct(
        ProductImageRepository $productImageRepository,
        CombinationRepository $combinationRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->combinationRepository = $combinationRepository;
    }

    public function getProductCoverUrl(ProductId $productId, ShopId $shopId): string
    {
        $imageId = $this->productImageRepository->getDefaultImageId($productId, $shopId);

        return $imageId ?
            $this->productImagePathFactory->getPath($imageId) :
            $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT)
        ;
    }

    public function getCombinationCoverUrl(CombinationId $combinationId, ShopId $shopId): string
    {
        $imageId = $this->productImageRepository->getPreviewCombinationProduct($combinationId);

        if ($imageId) {
            return $this->productImagePathFactory->getPath($imageId);
        }

        $productId = $this->combinationRepository->getProductId($combinationId);

        return $this->getProductCoverUrl($productId, $shopId);
    }
}
