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
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use Tools;

class ImageValidator
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->assertIsImage($filePath);
        $this->filePath = $filePath;
    }

    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function assertImageIsAllowedForUpload(): void
    {
        $maxFileSize = Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && filesize($this->filePath) > $maxFileSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $maxFileSize, $image->getSize()), UploadedImageConstraintException::EXCEEDED_SIZE);
        }

        if (!ImageManager::checkImageMemoryLimit($this->filePath)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }
    }

    /**
     * @param array $supportedExtensions
     *
     * @throws ImageUploadException
     */
    public function assertImageTypeIsSupported(array $supportedExtensions): void
    {
        $mime = ImageManager::getMimeType($this->filePath);

        //@todo: add other exceptions?
        if (!$mime) {
            throw new ImageUploadException(
                sprintf('Cannot recognize image type. Supported types are: %s', implode(',', $supportedExtensions))
            );
        }

        $extension = str_replace('image/', '', $mime);

        if (!in_array($extension, $supportedExtensions)) {
            throw new ImageUploadException(sprintf(
                'Unsupported image type "%s". Supported types are: %s',
                $extension,
                implode(',', $supportedExtensions)
            ));
        }
    }

    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    private function assertIsImage(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('"%s" is not a file', $filePath));
        }

        if (!ImageManager::isRealImage($filePath, mime_content_type($filePath))) {
            throw new UploadedImageConstraintException(sprintf('Image format "%s", not recognized, allowed formats are: .gif, .jpg, .png', $image->getClientOriginalExtension()), UploadedImageConstraintException::UNRECOGNIZED_FORMAT);
        }
    }
}
