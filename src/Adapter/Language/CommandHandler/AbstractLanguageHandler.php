<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Context;
use Db;
use ImageManager;
use ImageType;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use Shop;

/**
 * Encapsulates common legacy behavior for adding/editing language
 */
abstract class AbstractLanguageHandler
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
     * @param int $languageId
     * @param int[] $shopAssociation
     */
    protected function associateWithShops($languageId, array $shopAssociation)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $languageTable = Language::$definition['table'];

        if (!Shop::isTableAssociated($languageTable)) {
            return;
        }

        // Get list of shop id we want to exclude from asso deletion
        $excludeIds = $shopAssociation;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!Context::getContext()->employee->hasAuthOnShop($row['id_shop'])) {
                $excludeIds[] = $row['id_shop'];
            }
        }

        $excludeShopsCondtion = $excludeIds ?
            ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $excludeIds)) . ')' :
            ''
        ;

        Db::getInstance()->delete(
            $languageTable . '_shop',
            '`id_lang` = ' . (int) $languageId . $excludeShopsCondtion
        );

        $insert = [];
        foreach ($shopAssociation as $shopId) {
            $insert[] = [
                'id_lang' => (int) $languageId,
                'id_shop' => (int) $shopId,
            ];
        }

        Db::getInstance()->insert(
            $languageTable . '_shop',
            $insert,
            false,
            true,
            Db::INSERT_IGNORE
        );
    }
}
