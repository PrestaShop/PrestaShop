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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedFaviconExtensionException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedLogoImageExtensionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads logo image files
 */
class UploadLogosCommand
{
    const AVAILABLE_LOGO_IMAGE_EXTENSIONS = ['gif', 'jpg', 'jpeg', 'jpe', 'png'];

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
     */
    public function setUploadedHeaderLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->assertIsValidLogoImageExtension($uploadedHeaderLogo);

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
     * @throws NotSupportedLogoImageExtensionException
     */
    public function setUploadedInvoiceLogo(UploadedFile $uploadedInvoiceLogo)
    {
        $this->assertIsValidLogoImageExtension($uploadedInvoiceLogo);

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
     * @throws NotSupportedLogoImageExtensionException
     */
    public function setUploadedMailLogo(UploadedFile $uploadedMailLogo)
    {
        $this->assertIsValidLogoImageExtension($uploadedMailLogo);

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
     */
    public function setUploadedFavicon(UploadedFile $uploadedFavicon)
    {
        if ('ico' !== $uploadedFavicon->getClientOriginalExtension()) {
            throw new NotSupportedFaviconExtensionException(sprintf(
                'Not supported "%s" favicon extension. Supported extension is "ico".',
                $uploadedFavicon->getClientOriginalExtension()
            ));
        }

        $this->uploadedFavicon = $uploadedFavicon;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws NotSupportedLogoImageExtensionException
     */
    private function assertIsValidLogoImageExtension(UploadedFile $uploadedFile)
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        if (!in_array($extension, self::AVAILABLE_LOGO_IMAGE_EXTENSIONS, true)) {
            throw new NotSupportedLogoImageExtensionException(
                sprintf(
                    'Not supported "%s" image logo extension. Supported extensions are ""',
                    implode(',', self::AVAILABLE_LOGO_IMAGE_EXTENSIONS)
                )
            );
        }
    }
}
