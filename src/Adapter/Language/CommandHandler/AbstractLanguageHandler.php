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

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Context;
use ImageManager;
use ImageType;
use Language;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Encapsulates common legacy behavior for adding/editing language
 */
abstract class AbstractLanguageHandler extends AbstractObjectModelHandler
{
    /**
     * Copies "No picture" image for specific language
     *
     * @param IsoCode $isoCode
     * @param string $noPictureImagePath
     */
    protected function copyNoPictureImage(IsoCode $isoCode, $noPictureImagePath)
    {
        if (!($temporaryImage = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
            || !move_uploaded_file($noPictureImagePath, $temporaryImage)
        ) {
            return;
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'p/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to product directory'),
                CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR
            );
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'c/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to category directory'),
                CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR
            );
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'm/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to brand directory'),
                CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR
            );
        }

        $imagesTypes = ImageType::getImagesTypes('products');

        foreach ($imagesTypes as $imagesType) {
            $imageName = $isoCode->getValue() . '-default-' . stripslashes($imagesType['name']) . '.jpg';
            $imageWidth = $imagesType['width'];
            $imageHeight = $imagesType['height'];

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'p/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(
                    sprintf('An error occurred while copying "No Picture" image to product directory'),
                    CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR
                );
            }

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'c/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(
                    sprintf('An error occurred while copying "No Picture" image to category directory'),
                    CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR
                );
            }

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'm/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(
                    sprintf('An error occurred while copying "No Picture" image to brand directory'),
                    CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR
                );
            }
        }

        unlink($temporaryImage);
    }

    /**
     * @param int $languageId
     * @param string $newImagePath
     * @param string $imageDir
     */
    protected function uploadImage($languageId, $newImagePath, $imageDir)
    {
        $temporaryImage = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        if (!$temporaryImage) {
            return;
        }

        if (!move_uploaded_file($newImagePath, $temporaryImage)) {
            return;
        }

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($temporaryImage)) {
            throw new LanguageImageUploadingException(
                'Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.',
                LanguageImageUploadingException::MEMORY_LIMIT_RESTRICTION
            );
        }

        // Copy new image
        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . $imageDir . $languageId . '.jpg')) {
            throw new LanguageImageUploadingException(
                'An error occurred while uploading the image. Check your directory permissions.',
                LanguageImageUploadingException::UNEXPECTED_ERROR
            );
        }

        if (file_exists(_PS_LANG_IMG_DIR_ . $languageId . '.jpg')) {
            $shopId = Context::getContext()->shop->id;
            $currentFile = _PS_TMP_IMG_DIR_ . 'lang_mini_' . $languageId . '_' . $shopId . '.jpg';

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }

        unlink($temporaryImage);
    }

    /**
     * @param LanguageId $languageId
     *
     * @return Language
     */
    protected function getLegacyLanguageObject(LanguageId $languageId)
    {
        $language = new Language($languageId->getValue());

        if ($languageId->getValue() !== $language->id) {
            throw new LanguageNotFoundException(
                $languageId,
                sprintf('Language with id "%s" was not found', $languageId->getValue())
            );
        }

        return $language;
    }
}
