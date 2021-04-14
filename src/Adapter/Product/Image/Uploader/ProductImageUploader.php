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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\Uploader;

use Image;
use PrestaShop\PrestaShop\Adapter\Image\ImageGenerator;
use PrestaShop\PrestaShop\Adapter\Image\Uploader\AbstractImageUploader;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;

/**
 * Uploads product image to filesystem
 */
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
     * @var ImageGenerator
     */
    private $imageGenerator;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var bool
     */
    private $isLegacyImageMode;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     * @param ProductImagePathFactory $productImagePathFactory
     * @param int $contextShopId
     * @param ImageGenerator $imageGenerator
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $isLegacyImageMode
     * @param ProductImageRepository $productImageRepository
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration,
        ProductImagePathFactory $productImagePathFactory,
        int $contextShopId,
        ImageGenerator $imageGenerator,
        HookDispatcherInterface $hookDispatcher,
        bool $isLegacyImageMode,
        ProductImageRepository $productImageRepository
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->contextShopId = $contextShopId;
        $this->imageGenerator = $imageGenerator;
        $this->hookDispatcher = $hookDispatcher;
        $this->isLegacyImageMode = $isLegacyImageMode;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * @param Image $image
     * @param string $filePath
     *
     * @return string destination path of main image
     *
     * @throws CannotUnlinkImageException
     * @throws ImageUploadException
     * @throws ImageOptimizationException
     * @throws MemoryLimitException
     */
    public function upload(Image $image, string $filePath): string
    {
        $this->createDestinationDirectory($image);
        $destinationPath = $this->productImagePathFactory->getPath($image);
        $this->uploadFromTemp($filePath, $destinationPath);
        $this->imageGenerator->generateImagesByTypes($destinationPath, $this->productImageRepository->getProductImageTypes());

        $this->hookDispatcher->dispatchWithParameters(
            'actionWatermark',
            ['id_image' => (int) $image->id, 'id_product' => (int) $image->id_product]
        );

        $this->deleteCachedImages($image);

        return $destinationPath;
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    public function remove(Image $image): void
    {
        $destinationPath = $this->productImagePathFactory->getPath($image);
        $this->deleteCachedImages($image);
        $this->deleteGeneratedImages($destinationPath, $this->productImageRepository->getProductImageTypes());
    }

    /**
     * @param Image $image
     *
     * @throws ImageUploadException
     */
    private function createDestinationDirectory(Image $image): void
    {
        //@todo: refactor this to some new service which relies on ImagePathFactory & uses symfony Filesystem
        if ($this->isLegacyImageMode || $image->createImgFolder()) {
            return;
        }

        throw new ImageUploadException(sprintf(
            'Error occurred when trying to create directory for product #%d image',
            $image->id_product
        ));
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
            $this->productImagePathFactory->getHelperThumbnail($productId, $this->contextShopId),
            $this->productImagePathFactory->getCachedCover($productId),
        ];

        foreach ($cachedImages as $cachedImage) {
            if (!file_exists($cachedImage)) {
                continue;
            }

            if (!@unlink($cachedImage)) {
                throw new CannotUnlinkImageException(
                    sprintf(
                        'Failed to remove cached image "%s"',
                        $cachedImage
                    )
                );
            }
        }
    }

    /**
     * Note: we can't delete the whole folder here, or Image::delete will return an error
     * because it expects the original image and the folder to be present in order to remove
     * them correctly. So for now this service only handles removing generated image types.
     * When Image ObjectModel is no longer used, it could also remove the remaining files.
     *
     * @param string $imagePath
     * @param array $imageTypes
     *
     * @throws CannotUnlinkImageException
     */
    private function deleteGeneratedImages(string $imagePath, array $imageTypes): void
    {
        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $destinationExtension = '.jpg';
        $imageBaseName = rtrim($imagePath, '.' . $fileExtension);

        foreach ($imageTypes as $imageType) {
            $generatedImagePath = sprintf('%s-%s%s', $imageBaseName, stripslashes($imageType->name), $destinationExtension);
            if (!file_exists($generatedImagePath)) {
                continue;
            }

            if (!@unlink($generatedImagePath)) {
                throw new CannotUnlinkImageException(
                    sprintf(
                        'Failed to remove generated image "%s"',
                        $generatedImagePath
                    )
                );
            }
        }
    }
}
