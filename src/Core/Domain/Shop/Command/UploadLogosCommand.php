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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadLogosCommand
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
     * @return null|UploadedFile
     */
    public function getUploadedHeaderLogo()
    {
        return $this->uploadedHeaderLogo;
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    public function setUploadedHeaderLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->uploadedHeaderLogo = $uploadedHeaderLogo;
    }

    /**
     * @return null|UploadedFile
     */
    public function getUploadedInvoiceLogo()
    {
        return $this->uploadedInvoiceLogo;
    }

    /**
     * @param UploadedFile $uploadedInvoiceLogo
     */
    public function setUploadedInvoiceLogo(UploadedFile $uploadedInvoiceLogo)
    {
        $this->uploadedInvoiceLogo = $uploadedInvoiceLogo;
    }

    /**
     * @return null|UploadedFile
     */
    public function getUploadedMailLogo()
    {
        return $this->uploadedMailLogo;
    }

    /**
     * @param UploadedFile $uploadedMailLogo
     */
    public function setUploadedMailLogo(UploadedFile $uploadedMailLogo)
    {
        $this->uploadedMailLogo = $uploadedMailLogo;
    }

    /**
     * @return null|UploadedFile
     */
    public function getUploadedFavicon()
    {
        return $this->uploadedFavicon;
    }

    /**
     * @param UploadedFile $uploadedFavicon
     */
    public function setUploadedFavicon(UploadedFile $uploadedFavicon)
    {
        $this->uploadedFavicon = $uploadedFavicon;
    }
}
