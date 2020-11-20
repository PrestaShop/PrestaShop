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

use Configuration;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShopException;

/**
 * Responsible for resizing images based on provided types
 */
class ImageGenerator
{
    /**
     * @param string $imagePath
     * @param ImageType[] $imageTypes
     *
     * @return bool
     *
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     */
    public function generateImagesByTypes(string $imagePath, array $imageTypes): bool
    {
        $resized = true;

        try {
            foreach ($imageTypes as $imageType) {
                $resized &= $this->resize($imagePath, $imageType);
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
     *
     * @return bool
     */
    protected function resize(string $filePath, ImageType $imageType): bool
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('File "%s" does not exist', $filePath));
        }

        //@todo: hardcoded extension as it was in legacy code. Changing it would be a huge BC break.
        //@todo: in future we should consider using extension by mimeType
        $destinationExtension = '.jpg';
        $width = $imageType->width;
        $height = $imageType->height;

        if (Configuration::get('PS_HIGHT_DPI')) {
            $destinationExtension = '2x' . $destinationExtension;
            $width *= 2;
            $height *= 2;
        }

        return ImageManager::resize(
            $filePath,
            sprintf('%s-%s%s', rtrim($filePath, '.' . $fileExtension), stripslashes($imageType->name), $destinationExtension),
            $width,
            $height,
            trim(mime_content_type($filePath), 'image/')
        );
    }
}
