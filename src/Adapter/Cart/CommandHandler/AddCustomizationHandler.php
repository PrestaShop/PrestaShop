<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Configuration;
use CustomizationField;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\AddCustomizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShopException;
use Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Handles @var AddCustomizationCommand using legacy object model.
 */
#[AsCommandHandler]
final class AddCustomizationHandler extends AbstractCartHandler implements AddCustomizationHandlerInterface
{
    /**
     * If customization fields are not required and none of them are provided
     *  then no customizations are saved and null returned.
     *  Else, saved customizationId is returned or exception is thrown.
     *
     * @param AddCustomizationCommand $command
     *
     * @return CustomizationId|null
     *
     * @throws CartNotFoundException
     * @throws CustomizationConstraintException
     * @throws CustomizationException
     * @throws FileUploadException
     */
    public function handle(AddCustomizationCommand $command): ?CustomizationId
    {
        $productId = $command->getProductId()->getValue();

        $cart = $this->getCart($command->getCartId());
        $product = new Product($productId);

        $customizationFields = $product->getCustomizationFieldIds();
        $customizationValues = $command->getCustomizationValuesByFieldIds();

        foreach ($customizationFields as $customizationField) {
            $customizationFieldId = (int) $customizationField['id_customization_field'];
            $isRequired = (bool) $customizationField['required'];

            if ($isRequired && empty($customizationValues[$customizationFieldId])) {
                throw new CustomizationConstraintException(sprintf('Customization field #%s is required', $customizationFieldId), CustomizationConstraintException::FIELD_IS_REQUIRED);
            }

            if (empty($customizationValues[$customizationFieldId])) {
                continue;
            }

            try {
                if (CustomizationFieldType::TYPE_TEXT === (int) $customizationField['type']) {
                    $this->assertCustomTextField($customizationFieldId, $customizationValues[$customizationFieldId]);

                    $customizationId = $cart->addTextFieldToProduct(
                        $productId,
                        $customizationFieldId,
                        Product::CUSTOMIZE_TEXTFIELD,
                        $customizationValues[$customizationFieldId],
                        true
                    );
                } else {
                    $fileName = $this->uploadImage($customizationValues[$customizationFieldId]);

                    $customizationId = $cart->addPictureToProduct(
                        $productId,
                        $customizationFieldId,
                        CustomizationFieldType::TYPE_FILE,
                        $fileName,
                        true
                    );
                }

                if (false === $customizationId) {
                    throw new CustomizationException(sprintf('Failed to add customized data for customization field with id "%s"', $customizationFieldId));
                }
            } catch (PrestaShopException) {
                throw new CustomizationException(sprintf('An error occurred while trying to add customized data for customization field with id "%s"', $customizationFieldId));
            }
        }

        if (!isset($customizationId)) {
            return null;
        }

        return new CustomizationId((int) $customizationId);
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

        // @todo: check if copy is okay to use instead of move_uploaded_file(this fails creating new request from global later)
        // @todo: implement UploadedFile::move() instead of copy();
        if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !copy($file->getPathname(), $tmpName)) {
            throw new FileUploadException('An error occurred during the image upload process.');
        }
        $fileName = md5(uniqid('', true));
        $resized = ImageManager::resize($tmpName, _PS_UPLOAD_DIR_ . $fileName)
            && ImageManager::resize(
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
