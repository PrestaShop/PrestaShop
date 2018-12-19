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

use Db;
use ImageManager;
use ImageType;
use Language;
use Context;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\AddLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Shop;

/**
 * Handles command which adds new language using legacy object model
 *
 * @internal
 */
final class AddLanguageHandler implements AddLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddLanguageCommand $command)
    {
        $this->assertLanguageWithIsoCodeDoesNotExist($command->getIsoCode());

        $this->copyNoPictureImage($command);

        $language = $this->createLegacyLanguageObjectFromCommand($command);

        $this->uploadImages($language, $command);
        $this->addShopAssociation($language, $command);

        return new LanguageId((int) $language->id);
    }

    /**
     * @param IsoCode $isoCode
     *
     * @throws LanguageConstraintException
     */
    private function assertLanguageWithIsoCodeDoesNotExist(IsoCode $isoCode)
    {
        if (Language::getIdByIso($isoCode->getValue())) {
            throw new LanguageConstraintException(
                sprintf('Language with ISO code "%s" already exists', $isoCode->getValue()),
                LanguageConstraintException::INVALID_ISO_CODE
            );
        }
    }

    /**
     * Add language and shop association
     *
     * @param Language $language
     * @param AddLanguageCommand $command
     */
    private function addShopAssociation(Language $language, AddLanguageCommand $command)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $languageTable = Language::$definition['table'];

        if (!Shop::isTableAssociated($languageTable)) {
            return;
        }

        // Get list of shop id we want to exclude from asso deletion
        $excludeIds = $command->getShopAssociation();
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
            '`id_lang` = ' . (int) $language->id . $excludeShopsCondtion
        );

        $insert = [];
        foreach ($command->getShopAssociation() as $shopId) {
            $insert[] = [
                'id_lang' => (int) $language->id,
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

    /**
     * @param AddLanguageCommand $command
     *
     * @return Language
     */
    private function createLegacyLanguageObjectFromCommand(AddLanguageCommand $command)
    {
        $language = new Language();
        $language->name = $command->getName();
        $language->iso_code = $command->getIsoCode()->getValue();
        $language->language_code = $command->getTagIETF()->getValue();
        $language->date_format_lite = $command->getShortDateFormat();
        $language->date_format_full = $command->getFullDateFormat();
        $language->is_rtl = $command->isRtlLanguage();
        $language->active = $command->isActive();

        if (false === $language->add()) {
            throw new LanguageException(
                sprintf('Failed to add new language "%s"', $command->getName())
            );
        }

        return $language;
    }

    /**
     * Copies placeholder image for specific language
     *
     * @param AddLanguageCommand $command
     */
    private function copyNoPictureImage(AddLanguageCommand $command)
    {
        $isoCode = $command->getIsoCode()->getValue();

        if (!($temporaryImage = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
            || !move_uploaded_file($command->getNoPictureImagePath(), $temporaryImage)
        ) {
            return;
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'p/' . $isoCode . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to product directory'),
                CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR
            );
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'c/' . $isoCode . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to category directory'),
                CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR
            );
        }

        if (!ImageManager::resize($temporaryImage, _PS_IMG_DIR_ . 'm/' . $isoCode . '.jpg')) {
            throw new CopyingNoPictureException(
                sprintf('An error occurred while copying "No Picture" image to brand directory'),
                CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR
            );
        }

        $imagesTypes = ImageType::getImagesTypes('products');

        foreach ($imagesTypes as $imagesType) {
            $imageName = $isoCode . '-default-' . stripslashes($imagesType['name']) . '.jpg';
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
     * @param Language $language
     * @param AddLanguageCommand $command
     */
    private function uploadImages(Language $language, AddLanguageCommand $command)
    {

    }

    private function uploadImage($languageId, $imagePath, $dir)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES[$name], \Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->errors[] = $this->trans('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ', array(), 'Admin.Notifications.Error');
            }

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_ . $dir . $id . '.' . $this->imageType, (int) $width, (int) $height, ($ext ? $ext : $this->imageType))) {
                $this->errors[] = $this->trans('An error occurred while uploading the image.', array(), 'Admin.Notifications.Error');
            }

            if (count($this->errors)) {
                return false;
            }
            if ($this->afterImageUpload()) {
                unlink($tmp_name);

                return true;
            }

            return false;
        }

        return true;
    }
}
