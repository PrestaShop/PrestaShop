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
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageFileNotFoundException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;

/**
 * Responsible for validating image before upload
 */
class ImageValidator
{
    /**
     * @var int
     */
    protected $maxUploadSize;

    /**
     * @param int $maxUploadSizeInBytes
     */
    public function __construct(int $maxUploadSizeInBytes)
    {
        $this->maxUploadSize = $maxUploadSizeInBytes;
    }

    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function assertFileUploadLimits(string $filePath): void
    {
        $size = filesize($filePath);

        if ($this->maxUploadSize > 0 && $size > $this->maxUploadSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $this->maxUploadSize, $size), UploadedImageConstraintException::EXCEEDED_SIZE);
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
            throw new ImageFileNotFoundException(sprintf('Image file "%s" not found', $filePath));
        }

        $mime = mime_content_type($filePath);
        if (!ImageManager::isRealImage($filePath, $mime, $allowedMimeTypes)) {
            throw new UploadedImageConstraintException(sprintf('Image type "%s" is not allowed, allowed types are: %s', $mime, implode(',', $allowedMimeTypes)), UploadedImageConstraintException::UNRECOGNIZED_FORMAT);
        }
    }
}
