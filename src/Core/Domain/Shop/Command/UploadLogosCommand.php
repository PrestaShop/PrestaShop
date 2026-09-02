<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Shop\DTO\ShopLogoSettings;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedFaviconExtensionException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedLogoImageExtensionException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedMailAndInvoiceImageExtensionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads logo image files
 */
class UploadLogosCommand
{
    /**
     * @var UploadedFile|null
     */
    private $uploadedHeaderLogo;

    /**
     * @var UploadedFile|null
     */
    private $uploadedInvoiceLogo;

    /**
     * @var UploadedFile|null
     */
    private $uploadedMailLogo;

    /**
     * @var UploadedFile|null
     */
    private $uploadedFavicon;

    /**
     * @return UploadedFile|null
     */
    public function getUploadedHeaderLogo()
    {
        return $this->uploadedHeaderLogo;
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     *
     * @throws NotSupportedLogoImageExtensionException
     * @throws FileUploadException
     */
    public function setUploadedHeaderLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->assertIsValidLogoImageExtension($uploadedHeaderLogo);
        $this->assertNativeFileValidationDoesNotFail($uploadedHeaderLogo);

        $this->uploadedHeaderLogo = $uploadedHeaderLogo;
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedInvoiceLogo()
    {
        return $this->uploadedInvoiceLogo;
    }

    /**
     * @param UploadedFile $uploadedInvoiceLogo
     *
     * @throws NotSupportedMailAndInvoiceImageExtensionException
     * @throws FileUploadException
     */
    public function setUploadedInvoiceLogo(UploadedFile $uploadedInvoiceLogo)
    {
        $this->assertIsValidMailAndInvoiceImageExtension($uploadedInvoiceLogo);
        $this->assertNativeFileValidationDoesNotFail($uploadedInvoiceLogo);

        $this->uploadedInvoiceLogo = $uploadedInvoiceLogo;
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedMailLogo()
    {
        return $this->uploadedMailLogo;
    }

    /**
     * @param UploadedFile $uploadedMailLogo
     *
     * @throws NotSupportedMailAndInvoiceImageExtensionException
     * @throws FileUploadException
     */
    public function setUploadedMailLogo(UploadedFile $uploadedMailLogo)
    {
        $this->assertIsValidMailAndInvoiceImageExtension($uploadedMailLogo);
        $this->assertNativeFileValidationDoesNotFail($uploadedMailLogo);

        $this->uploadedMailLogo = $uploadedMailLogo;
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedFavicon()
    {
        return $this->uploadedFavicon;
    }

    /**
     * @param UploadedFile $uploadedFavicon
     *
     * @throws NotSupportedFaviconExtensionException
     * @throws FileUploadException
     */
    public function setUploadedFavicon(UploadedFile $uploadedFavicon)
    {
        if (ShopLogoSettings::AVAILABLE_ICON_IMAGE_EXTENSION !== $uploadedFavicon->getClientOriginalExtension()) {
            throw new NotSupportedFaviconExtensionException(sprintf('Not supported "%s" favicon extension. Supported extension is "ico".', $uploadedFavicon->getClientOriginalExtension()));
        }

        $this->assertNativeFileValidationDoesNotFail($uploadedFavicon);

        $this->uploadedFavicon = $uploadedFavicon;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws NotSupportedLogoImageExtensionException
     */
    private function assertIsValidLogoImageExtension(UploadedFile $uploadedFile): void
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        if (!in_array($extension, ShopLogoSettings::AVAILABLE_LOGO_IMAGE_EXTENSIONS, true)) {
            throw new NotSupportedLogoImageExtensionException(sprintf(
                'Not supported "%s" image logo extension. Supported extensions are "%s"',
                $extension,
                implode(',', ShopLogoSettings::AVAILABLE_LOGO_IMAGE_EXTENSIONS
                )));
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws NotSupportedMailAndInvoiceImageExtensionException
     */
    private function assertIsValidMailAndInvoiceImageExtension(UploadedFile $uploadedFile): void
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        if (!in_array($extension, ShopLogoSettings::AVAILABLE_MAIL_AND_INVOICE_LOGO_IMAGE_EXTENSIONS, true)) {
            throw new NotSupportedMailAndInvoiceImageExtensionException(sprintf(
                'Not supported "%s" image logo extension. Supported extensions are "%s"',
                $extension,
                implode(',', ShopLogoSettings::AVAILABLE_MAIL_AND_INVOICE_LOGO_IMAGE_EXTENSIONS
                )));
        }
    }

    /**
     * Checks if native file validation does not fail.
     *
     * @param UploadedFile $uploadedFile
     *
     * @throws FileUploadException
     */
    private function assertNativeFileValidationDoesNotFail(UploadedFile $uploadedFile): void
    {
        $errorCode = $uploadedFile->getError();

        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new FileUploadException($uploadedFile->getErrorMessage(), $errorCode);
        }
    }
}
