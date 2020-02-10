<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Configuration;
use CustomizationField;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShopException;
use Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Adds product customization fields data using legacy object model
 */
final class AddCustomizationFieldsHandler extends AbstractCartHandler implements AddCustomizationFieldsHandlerInterface
{
    /**
     * If customization fields are not required and none of them are provided
     *  then no customizations are saved and null returned.
     *  Else, saved customizationId is returned or exception is thrown.
     *
     * @param AddCustomizationFieldsCommand $command
     *
     * @return int
     *
     * @throws CartNotFoundException
     * @throws CustomizationConstraintException
     * @throws CustomizationException
     * @throws FileUploadException
     */
    public function handle(AddCustomizationFieldsCommand $command): ?CustomizationId
    {
        $productId = $command->getProductId()->getValue();

        $cart = $this->getCart($command->getCartId());
        $product = new Product($productId);

        $customizationFields = $product->getCustomizationFieldIds();
        $customizations = $command->getCustomizationsByFieldIds();

        foreach ($customizationFields as $customizationField) {
            $customizationFieldId = (int) $customizationField['id_customization_field'];
            $isRequired = (bool) $customizationField['required'];

            if ($isRequired && empty($customizations[$customizationFieldId])) {
                throw new CustomizationConstraintException(sprintf('Customization field #%s is required', $customizationFieldId), CustomizationConstraintException::FIELD_IS_REQUIRED);
            }

            if (empty($customizations[$customizationFieldId])) {
                continue;
            }

            try {
                if (Product::CUSTOMIZE_TEXTFIELD == $customizationField['type']) {
                    $this->assertCustomTextField($customizationFieldId, $customizations[$customizationFieldId]);

                    $customizationId = $cart->addTextFieldToProduct(
                        $productId,
                        $customizationFieldId,
                        Product::CUSTOMIZE_TEXTFIELD,
                        $customizations[$customizationFieldId],
                        true
                    );
                } else {
                    $fileName = $this->uploadImage($customizations[$customizationFieldId]);

                    $customizationId = $cart->addPictureToProduct(
                        $productId,
                        $customizationFieldId,
                        Product::CUSTOMIZE_FILE,
                        $fileName,
                        true
                    );
                }

                if (false === $customizationId) {
                    throw new CustomizationException(sprintf('Failed to add customized data for customization field with id "%s"', $customizationFieldId));
                }
            } catch (PrestaShopException $e) {
                throw new CustomizationException(sprintf('An error occurred while trying to add customized data for customization field with id "%s"', $customizationFieldId));
            }
        }

        if (!isset($customizationId)) {
            return null;
        }

        return new CustomizationId($customizationId);
    }

    /**
     * Uploads image from customized field
     *
     * @param UploadedFile $file
     *
     * @return string
     *
     * @throws FileUploadException
     */
    private function uploadImage(UploadedFile $file): string
    {
        $this->validateUpload($file);

        //@todo: check if copy is okay to use instead of move_uploaded_file(this fails creating new request from global later)
        //@todo: implement UploadedFile::move() instead of copy();
        if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !copy($file->getPathname(), $tmpName)) {
            throw new FileUploadException('An error occurred during the image upload process.');
        }
        $fileName = md5(uniqid('', true));
        $resized = ImageManager::resize($tmpName, _PS_UPLOAD_DIR_ . $fileName) &&
            ImageManager::resize(
                $tmpName,
                _PS_UPLOAD_DIR_ . $fileName . '_small',
                (int) Configuration::get('PS_PRODUCT_PICTURE_WIDTH'),
                (int) Configuration::get('PS_PRODUCT_PICTURE_HEIGHT')
            );

        if (!$resized) {
            throw new FileUploadException('An error occurred when resizing the uploaded image');
        }

        unlink($tmpName);

        return $fileName;
    }

    /**
     * Validates uploaded image
     *
     * @param UploadedFile $file
     *
     * @throws FileUploadException
     */
    private function validateUpload(UploadedFile $file): void
    {
        $maxFileSize = (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');

        if ((int) $maxFileSize > 0 && $file->getSize() > (int) $maxFileSize) {
            throw new FileUploadException(sprintf('Image is too large (%s kB). Maximum allowed: %s kB', $file->getSize() / 1024, $maxFileSize / 1024), UPLOAD_ERR_FORM_SIZE);
        }

        if (!ImageManager::isRealImage($file->getPathname(), $file->getType()) || !ImageManager::isCorrectImageFileExt($file->getClientOriginalName(), null) || preg_match('/\%00/', $file->getClientOriginalName())) {
            throw new FileUploadException('Image format not recognized, allowed formats are: .gif, .jpg, .png', UPLOAD_ERR_EXTENSION);
        }

        if ($file->getError()) {
            throw new FileUploadException('Error while uploading image', $file->getError());
        }
    }

    /**
     * @param int $customFieldId
     * @param string $value
     *
     * @throws CustomizationConstraintException
     */
    private function assertCustomTextField(int $customFieldId, string $value)
    {
        $customization = new CustomizationField($customFieldId);

        if ($customization->required && '' === $value) {
            throw new CustomizationConstraintException(sprintf('Customization field #%s is required', $customFieldId), CustomizationConstraintException::FIELD_IS_REQUIRED);
        }

        if (strlen($value) > CustomizationSettings::MAX_TEXT_LENGTH) {
            throw new CustomizationConstraintException(sprintf('Customization field #%s value is too long', $customFieldId), CustomizationConstraintException::FIELD_IS_TOO_LONG);
        }
    }
}
