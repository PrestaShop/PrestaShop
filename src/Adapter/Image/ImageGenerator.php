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

namespace PrestaShop\PrestaShop\Adapter\Image;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShopException;

/**
 * Responsible for resizing images based on provided types
 */
class ImageGenerator
{
    public function __construct(
        private readonly ImageFormatConfiguration $imageFormatConfiguration
    ) {
    }

    /**
     * @param string $imagePath
     * @param ImageType[] $imageTypes
     * @param int $imageId
     *
     * @return bool
     *
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     */
    public function generateImagesByTypes(string $imagePath, array $imageTypes, int $imageId = 0): bool
    {
        $resized = true;

        try {
            foreach ($imageTypes as $imageType) {
                $resized &= $this->resize($imagePath, $imageType, $imageId);
            }
        } catch (PrestaShopException $e) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        if (!$resized) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        return (bool) $resized;
    }

    /**
     * Resizes the image depending on its type
     *
     * @param string $filePath
     * @param ImageType $imageType
     * @param int $imageId
     *
     * @return bool
     */
    protected function resize(string $filePath, ImageType $imageType, int $imageId = 0): bool
    {
        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('File "%s" does not exist', $filePath));
        }

        /*
         * Let's resolve which formats we will use for image generation.
         *
         * In case of .jpg images, the actual format inside is decided by ImageManager.
         */
        $configuredImageFormats = $this->imageFormatConfiguration->getGenerationFormats();

        $result = true;

        foreach ($configuredImageFormats as $imageFormat) {
            // For JPG images, we let Imagemanager decide what to do and choose between JPG/PNG.
            // For webp and avif extensions, we want it to follow our command and ignore the original format.
            $forceFormat = ($imageFormat !== 'jpg');
            if (!ImageManager::resize(
                $filePath,
                sprintf('%s-%s.%s', dirname($filePath) . DIRECTORY_SEPARATOR . $imageId, stripslashes($imageType->name), $imageFormat),
                $imageType->width,
                $imageType->height,
                $imageFormat,
                $forceFormat
            )) {
                $result = false;
            }
        }

        return $result;
    }
}
