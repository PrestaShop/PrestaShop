<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use ImageManager;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\TmpImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads temporary image
 */
final class TmpImageUploader implements TmpImageUploaderInterface
{
    /**
     * Uploads image to temporary folder and returns its path
     *
     * @param UploadedFile $file
     *
     * @return string uploaded image path
     *
     * @throws ImageUploadException
     * @throws ImageOptimizationException
     */
    public function upload(UploadedFile $file): string
    {
        $this->validateFile($file);

        do {
            $tmp_name = uniqid() . '.jpg';
        } while (file_exists(_PS_TMP_IMG_DIR_ . $tmp_name));
        if (!ImageManager::resize($file->getPathname(), _PS_TMP_IMG_DIR_ . $tmp_name)) {
            throw new ImageOptimizationException(sprintf('Cannot resize the image into %s', _PS_TMP_IMG_DIR_));
        }
        @unlink($file->getPathname());

        return _PS_TMP_IMG_ . $tmp_name;
    }

    /**
     * @param UploadedFile $file
     *
     * @throws ImageUploadException
     */
    private function validateFile(UploadedFile $file)
    {
        $isValidExtension = preg_match('/(jpe?g|gif|png)$/', $file->getClientOriginalExtension());
        $isRealImage = ImageManager::isRealImage($file->getPathname(), $file->getMimeType());

        if (!$file->isValid() || !$isValidExtension || !$isRealImage) {
            throw new ImageUploadException('Cannot upload image. The file is invalid');
        }
    }
}
