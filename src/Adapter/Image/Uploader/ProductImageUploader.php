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
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;

final class ProductImageUploader extends AbstractImageUploader
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
     * @var array
     */
    private $contextShopIdsList;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     * @param ProductImagePathFactory $productImagePathFactory
     * @param array $contextShopIdsList
     * @param int $contextShopId
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration,
        ProductImagePathFactory $productImagePathFactory,
        array $contextShopIdsList,
        int $contextShopId
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->contextShopIdsList = $contextShopIdsList;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(
        Image $image,
        string $filePath,
        string $format
    ): void {
        $this->checkMemory($filePath);
        $this->productImagePathFactory->createDestinationDirectory($image);
        $this->copyToDestination($filePath, $image);

        $this->generateDifferentSizeImages(
            $this->productImagePathFactory->getBasePath($image, false),
            'products',
            $format
        );

        Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $image->id_product]);
        //@Todo: wait for multishop specs
        $image->associateTo($this->contextShopIdsList);
        $this->deleteOldGeneratedImages($image);
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    private function deleteOldGeneratedImages(Image $image): void
    {
        $oldGeneratedImages = [
            _PS_TMP_IMG_DIR_ . 'product_' . (int) $image->id . '.jpg',
            _PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $image->id_product . '_' . $this->contextShopId . '.jpg',
        ];

        foreach ($oldGeneratedImages as $oldImage) {
            if (file_exists($oldImage)) {
                try {
                    unlink($oldImage);
                } catch (ErrorException $e) {
                    throw new CannotUnlinkImageException(
                        sprintf(
                            'Failed to remove old generated image "%s"',
                            $oldImage
                        ),
                        0,
                        $e
                    );
                }
            }
        }
    }

    /**
     * @param string $tmpImageName
     * @param Image $image
     *
     * @throws ImageOptimizationException
     */
    private function copyToDestination(string $tmpImageName, Image $image)
    {
        if (!ImageManager::resize($tmpImageName, $this->productImagePathFactory->getBasePath($image, true))) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }
    }

    /**
     * Evaluate the memory required to resize the image: if it's too much, you can't resize it.
     *
     * @param string $tmpImageName
     *
     * @throws MemoryLimitException
     */
    private function checkMemory(string $tmpImageName): void
    {
        if (!ImageManager::checkImageMemoryLimit($tmpImageName)) {
            throw new MemoryLimitException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.');
        }
    }

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws ImageNotFoundException
     */
    private function loadImageEntity(ImageId $imageId): Image
    {
        $imageIdValue = $imageId->getValue();
        $image = new Image($imageIdValue);

        if ((int) $image->id !== $imageIdValue) {
            throw new ImageNotFoundException(sprintf(
                'Image entity with id #%s does not exist',
                $imageIdValue
            ));
        }

        return $image;
    }
}
