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

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ErrorException;
use Hook;
use Image;
use PrestaShop\PrestaShop\Adapter\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Adapter\Product\ProductImagePathFactory;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;

//@todo: do we really need an interface? depends if we use it in controller or in command handler
class ProductImageUploader extends AbstractImageUploader
{
    /**
     * @var UploadSizeConfigurationInterface
     */
    private $uploadSizeConfiguration;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     * @param ProductImagePathFactory $productImagePathFactory
     * @param int $contextShopId
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration,
        ProductImagePathFactory $productImagePathFactory,
        int $contextShopId
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(Image $image, string $filePath): void
    {
        $this->productImagePathFactory->createDestinationDirectory($image);

        //@todo: this will unlink the image. Can we trust that the $filePath is in temp?
        $this->uploadFromTemp($filePath, $this->productImagePathFactory->getBasePath($image, true));
        $this->generateDifferentSizeImages($this->productImagePathFactory->getBasePath($image, false), 'products');

        Hook::exec('actionWatermark', ['id_image' => (int) $image->id, 'id_product' => (int) $image->id_product]);
        //@todo: moved multishop association from here to Repository when Image objModel is created
        $this->deleteCachedImages($image);
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    private function deleteCachedImages(Image $image): void
    {
        $productId = (int) $image->id_product;

        $cachedImages = [
            $this->productImagePathFactory->getCachedCover($productId, $this->contextShopId),
            $this->productImagePathFactory->getCachedThumbnail($productId),
        ];

        foreach ($cachedImages as $cachedImage) {
            if (file_exists($cachedImage)) {
                try {
                    unlink($cachedImage);
                } catch (ErrorException $e) {
                    //@todo: do we really need to fail on cached images deletion? It was suppressed in legacy
                    throw new CannotUnlinkImageException(
                        sprintf(
                            'Failed to remove cached image "%s"',
                            $cachedImage
                        ),
                        0,
                        $e
                    );
                }
            }
        }
    }
}
