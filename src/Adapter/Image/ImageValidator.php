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
use ImageManagerCore;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use Tools;

class ImageValidator
{
    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function assertFileUploadLimits(string $filePath): void
    {
        $maxFileSize = Tools::getMaxUploadSize();
        $size = filesize($filePath);

        if ($maxFileSize > 0 && $size > $maxFileSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $maxFileSize, $size), UploadedImageConstraintException::EXCEEDED_SIZE);
        }

        if (!ImageManager::checkImageMemoryLimit($filePath)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }
    }

    /**
     * @param string $filePath
     * @param array $allowedMimeTypes
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function assertIsValidImageType(string $filePath, array $allowedMimeTypes = null): void
    {
        if (!$allowedMimeTypes) {
            $allowedMimeTypes = ImageManagerCore::MIME_TYPE_SUPPORTED;
        }

        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('"%s" is not a file', $filePath));
        }

        $mime = mime_content_type($filePath);
        if (!ImageManager::isRealImage($filePath, mime_content_type($filePath), $allowedMimeTypes)) {
            throw new UploadedImageConstraintException(sprintf('Image type "%s" is not allowed, allowed Types are: %s', $mime, implode(',', $allowedMimeTypes)), UploadedImageConstraintException::UNRECOGNIZED_FORMAT);
        }
    }
}
