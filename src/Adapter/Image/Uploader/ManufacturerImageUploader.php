<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Configuration;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads manufacturer logo image
 */
final class ManufacturerImageUploader extends AbstractImageUploader
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
        $generateHighDpiImages = (bool) Configuration::get('PS_HIGHT_DPI');

        try {
            /* Generate images with different size */
            if (isset($_FILES) &&
                count($_FILES) &&
                file_exists(_PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')
            ) {
                $imageTypes = ImageType::getImagesTypes('manufacturers');

                foreach ($imageTypes as $imageType) {
                    $resized &= ImageManager::resize(
                        _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg',
                        _PS_MANU_IMG_DIR_ . $manufacturerId . '-' . stripslashes($imageType['name']) . '.jpg',
                        (int) $imageType['width'],
                        (int) $imageType['height']
                    );

                    if ($generateHighDpiImages) {
                        $resized &= ImageManager::resize(
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg',
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '-' . stripslashes($imageType['name']) . '2x.jpg',
                            (int) $imageType['width'] * 2,
                            (int) $imageType['height'] * 2
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
