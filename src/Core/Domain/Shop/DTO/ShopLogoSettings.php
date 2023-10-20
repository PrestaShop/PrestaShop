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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\DTO;

/**
 * Holds default settings for shop logos.
 */
class ShopLogoSettings
{
    public const HEADER_LOGO_FILE_NAME = 'PS_LOGO';

    public const MAIL_LOGO_FILE_NAME = 'PS_LOGO_MAIL';

    public const INVOICE_LOGO_FILE_NAME = 'PS_LOGO_INVOICE';

    public const FAVICON_FILE_NAME = 'PS_FAVICON';

    /**
     * @var array<int, string> List of available image mime types
     */
    public const AVAILABLE_LOGO_IMAGE_EXTENSIONS = ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'webp', 'svg'];

    /**
     * @var array<int, string> List of available image mime types for mail and invoice
     */
    public const AVAILABLE_MAIL_AND_INVOICE_LOGO_IMAGE_EXTENSIONS = ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'webp'];

    /**
     * @var string - a type used for icon images for shop logo
     */
    public const AVAILABLE_ICON_IMAGE_EXTENSION = 'ico';

    /**
     * Gets the list of available extensions with dot attached to the front of the extension
     *
     * @param string $fieldName optional configuration key
     *
     * @return array<int, string>
     */
    public function getLogoImageExtensionsWithDot(string $fieldName = '')
    {
        $mimeTypes = [];
        $availableExtensions = (in_array($fieldName, ['PS_LOGO_MAIL', 'PS_LOGO_INVOICE'])) ? ShopLogoSettings::AVAILABLE_MAIL_AND_INVOICE_LOGO_IMAGE_EXTENSIONS : ShopLogoSettings::AVAILABLE_LOGO_IMAGE_EXTENSIONS;

        foreach ($availableExtensions as $imageExtension) {
            $mimeTypes[] = '.' . $imageExtension;
        }

        return $mimeTypes;
    }

    /**
     * Gets icon image extension with dot attached to the front.
     *
     * @return string
     */
    public function getIconImageExtensionWithDot()
    {
        return '.' . self::AVAILABLE_ICON_IMAGE_EXTENSION;
    }
}
