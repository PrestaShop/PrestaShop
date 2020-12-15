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

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductImagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductImage;

/**
 * Handles @see GetProductImages query
 */
final class GetProductImagesHandler implements GetProductImagesHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @param ProductImageRepository $productImageRepository
     */
    public function __construct(
        ProductImageRepository $productImageRepository
    ) {
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductImages $query): array
    {
        //@todo: optimize. add pagination options to query?
        $images = $this->productImageRepository->getImages($query->getProductId());

        return $this->formatImages($images);
    }

    /**
     * @param Image[] $images
     *
     * @return ProductImage[]
     */
    private function formatImages(array $images): array
    {
        $productImages = [];

        foreach ($images as $image) {
            $productImages[] = new ProductImage(
                (int) $image->id,
                (bool) $image->cover,
                (int) $image->position,
                $image->legend,
                sprintf('%s.%s', $image->getImgPath(), $image->image_format)
            );
        }

        return $productImages;
    }
}
