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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use Configuration;
use Context;
use ImageManager;
use ImageType;
use Language;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\ToggleLanguageStatusCommandInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
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
            || !copy($noPictureImagePath, $temporaryImage)
        ) {
            return;
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'p/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to product directory'), CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR);
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'c/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to category directory'), CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR);
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'm/' . $isoCode->getValue() . '.jpg')) {
            throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to brand directory'), CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR);
        }

        $imagesTypes = ImageType::getImagesTypes('products');

        foreach ($imagesTypes as $imagesType) {
            $imageName = $isoCode->getValue() . '-default-' . stripslashes($imagesType['name']) . '.jpg';
            $imageWidth = $imagesType['width'];
            $imageHeight = $imagesType['height'];

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'p/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to product directory'), CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR);
            }

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'c/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to category directory'), CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR);
            }

            if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'm/' . $imageName, $imageWidth, $imageHeight)) {
                throw new CopyingNoPictureException(sprintf('An error occurred while copying "No Picture" image to brand directory'), CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR);
            }
        }

        unlink($noPictureImagePath);
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

        if (!copy($newImagePath, $temporaryImage)) {
            return;
        }

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($temporaryImage)) {
            throw new LanguageImageUploadingException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.', LanguageImageUploadingException::MEMORY_LIMIT_RESTRICTION);
        }

        // Copy new image
        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . $imageDir . $languageId . '.jpg')) {
            throw new LanguageImageUploadingException('An error occurred while uploading the image. Check your directory permissions.', LanguageImageUploadingException::UNEXPECTED_ERROR);
        }

        if (file_exists(_PS_LANG_IMG_DIR_ . $languageId . '.jpg')) {
            $shopId = Context::getContext()->shop->id;
            $currentFile = _PS_TMP_IMG_DIR_ . 'lang_mini_' . $languageId . '_' . $shopId . '.jpg';

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }

        unlink($newImagePath);
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
            throw new LanguageNotFoundException($languageId, sprintf('Language with id "%s" was not found', $languageId->getValue()));
        }

        return $language;
    }

    /**
     * @param Language $language
     */
    protected function assertLanguageIsNotInUse(Language $language)
    {
        if ($language->id === (int) Context::getContext()->language->id) {
            throw new DefaultLanguageException(sprintf('Used language "%s" cannot be deleted', $language->iso_code), DefaultLanguageException::CANNOT_DELETE_IN_USE_ERROR);
        }
    }

    /**
     * @param Language $language
     * @param ToggleLanguageStatusCommandInterface $command
     */
    protected function assertLanguageIsNotDefault(Language $language, ToggleLanguageStatusCommandInterface $command = null)
    {
        if ($command != null && true === $command->getStatus()) {
            return;
        }

        if ($language->id === (int) Configuration::get('PS_LANG_DEFAULT')) {
            throw new DefaultLanguageException(sprintf('Default language "%s" cannot be disabled', $language->iso_code), DefaultLanguageException::CANNOT_DISABLE_ERROR);
        }
    }
}
