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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\QueryHandler;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImageProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler\GetProductImagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImages;

final class GetProductImagesHandler extends AbstractProductHandler implements GetProductImagesHandlerInterface
{
    /**
     * @var ProductImageProvider
     */
    private $productImageProvider;

    /**
     * @param ProductImageProvider $productImageProvider
     */
    public function __construct(ProductImageProvider $productImageProvider)
    {
        $this->productImageProvider = $productImageProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetProductImages $query): ProductImages
    {
        $images = $this->productImageProvider->getImages($query->getProductId()->getValue());

        $productImages = [];
        foreach ($images as $image) {
            $imageId = (int) $image['id_image'];

            $productImages[] = new ProductImage(
                $imageId,
                (int) $image['id_product'],
                //@todo: previously used _THEME_PROD_DIR_ . $image->getImgPath. Do we really want to load the image again to get the path?
                //   AdminModelAdapter::522 -> ProductDataProvider::152
                _THEME_PROD_DIR_ . Image::getImgFolderStatic($imageId) . $imageId,
                $image['legend'],
                (int) $image['position'],
                (bool) $image['cover']
            );
        }

        return new ProductImages($productImages);
    }
}
