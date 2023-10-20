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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CategoryThumbnailImageUploader.
 */
final class CategoryThumbnailImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws UploadedImageConstraintException
     * @throws ImageUploadException
     * @throws ImageOptimizationException
     */
    public function upload($id, UploadedFile $uploadedImage)
    {
        $imagesTypes = ImageType::getImagesTypes('categories');
        $formattedName = ImageType::getFormattedName('small');

        foreach ($imagesTypes as $k => $imagesType) {
            if ($formattedName !== $imagesType['name']) {
                continue;
            }

            $this->checkImageIsAllowedForUpload($uploadedImage);

            $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmpName) {
                throw new ImageUploadException('Failed to create temporary category thumbnail image file');
            }

            if (!move_uploaded_file($uploadedImage->getPathname(), $tmpName)) {
                throw new ImageUploadException('Failed to upload category thumbnail image');
            }

            if (!ImageManager::resize(
                $tmpName,
                _PS_CAT_IMG_DIR_ . $id . '-' . stripslashes($imagesType['name']) . '.jpg',
                (int) $imagesType['width'],
                (int) $imagesType['height']
            )) {
                throw new ImageOptimizationException('Failed to optimize category thumbnail image after uploading');
            }

            if (($imageSize = getimagesize($tmpName)) && is_array($imageSize)) {
                ImageManager::resize(
                    $tmpName,
                    _PS_CAT_IMG_DIR_ . $id . '_thumb.jpg',
                    (int) $imageSize[0],
                    (int) $imageSize[1]
                );
            }

            unlink($tmpName);
        }
    }
}
