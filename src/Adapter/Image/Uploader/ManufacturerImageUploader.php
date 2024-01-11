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

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads manufacturer logo image
 */
final class ManufacturerImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function upload($manufacturerId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.');
        }
        // Copy new image
        if (!ImageManager::resize($temporaryImageName, _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }

        $this->generateDifferentSizeImages($manufacturerId);
    }

    /**
     * @param int $manufacturerId
     *
     * @return bool
     */
    private function generateDifferentSizeImages($manufacturerId)
    {
        $resized = true;

        try {
            /* Generate images with different size */
            if (count($_FILES) &&
                file_exists(_PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')
            ) {
                $imageTypes = ImageType::getImagesTypes('manufacturers');
                $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();

                foreach ($imageTypes as $imageType) {
                    foreach ($configuredImageFormats as $imageFormat) {
                        $resized &= ImageManager::resize(
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg',
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '-' . stripslashes($imageType['name']) . '.' . $imageFormat,
                            (int) $imageType['width'],
                            (int) $imageType['height'],
                            $imageFormat
                        );
                    }
                }

                $currentLogo = _PS_TMP_IMG_DIR_ . 'manufacturer_mini_' . $manufacturerId . '.jpg';

                if ($resized && file_exists($currentLogo)) {
                    unlink($currentLogo);
                }
            }
        } catch (PrestaShopException $e) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        if (!$resized) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        return $resized;
    }
}
