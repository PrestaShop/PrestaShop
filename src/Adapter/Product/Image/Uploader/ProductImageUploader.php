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
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem as PsFileSystem;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Uploads product image to filesystem
 */
class ProductImageUploader extends AbstractImageUploader
{
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
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @param ProductImagePathFactory $productImagePathFactory
     * @param int $contextShopId
     * @param ImageGenerator $imageGenerator
     * @param HookDispatcherInterface $hookDispatcher
     * @param ProductImageRepository $productImageRepository
     */
    public function __construct(
        ProductImagePathFactory $productImagePathFactory,
        int $contextShopId,
        ImageGenerator $imageGenerator,
        HookDispatcherInterface $hookDispatcher,
        ProductImageRepository $productImageRepository
    ) {
        $this->productImagePathFactory = $productImagePathFactory;
        $this->contextShopId = $contextShopId;
        $this->imageGenerator = $imageGenerator;
        $this->hookDispatcher = $hookDispatcher;
        $this->productImageRepository = $productImageRepository;
        $this->fileSystem = new Filesystem();
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
        $imageId = new ImageId((int) $image->id);
        $productId = (int) $image->id_product;

        $this->createDestinationDirectory($imageId, $productId);
        $destinationPath = $this->productImagePathFactory->getPath($imageId);
        $this->uploadFromTemp($filePath, $destinationPath);
        $this->imageGenerator->generateImagesByTypes($destinationPath, $this->productImageRepository->getProductImageTypes(), $imageId->getValue());

        $this->hookDispatcher->dispatchWithParameters(
            'actionWatermark',
            ['id_image' => $imageId->getValue(), 'id_product' => $productId]
        );

        $this->deleteCachedImages($productId);

        return $destinationPath;
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    public function remove(Image $image): void
    {
        $destinationPath = $this->productImagePathFactory->getPath(new ImageId((int) $image->id));
        $this->deleteCachedImages((int) $image->id_product);
        $this->deleteGeneratedImages($destinationPath, $this->productImageRepository->getProductImageTypes());
    }

    /**
     * @param ImageId $imageId
     * @param int $productId
     *
     * @throws ImageUploadException
     */
    private function createDestinationDirectory(ImageId $imageId, int $productId): void
    {
        $imageFolder = $this->productImagePathFactory->getImageFolder($imageId);
        if (is_dir($imageFolder)) {
            return;
        }

        try {
            $this->fileSystem->mkdir($imageFolder, PsFileSystem::DEFAULT_MODE_FOLDER);
        } catch (IOException $e) {
            throw new ImageUploadException(sprintf(
                'Error occurred when trying to create directory for product #%d image',
                $productId
            ));
        }
    }

    /**
     * @param int $productId
     *
     * @throws CannotUnlinkImageException
     */
    private function deleteCachedImages(int $productId): void
    {
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
