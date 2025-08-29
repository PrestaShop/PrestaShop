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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use PrestaShop\PrestaShop\Core\Domain\Carrier\CarrierLogoFileUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierLogoUploadFailedException;
use PrestaShop\PrestaShop\Core\File\Exception\FileException;

/**
 * Uploads carrier logo file
 */
class CarrierLogoFileUploader implements CarrierLogoFileUploaderInterface
{
    public function upload(string $filePath, int $id): void
    {
        try {
            move_uploaded_file($filePath, _PS_SHIP_IMG_DIR_ . $id . '.jpg');
        } catch (FileException) {
            throw new CarrierLogoUploadFailedException(sprintf('Failed to copy the file %s.', $filePath));
        }
    }

    public function deleteOldFile(int $id): void
    {
        if (file_exists(_PS_SHIP_IMG_DIR_ . $id . '.jpg')) {
            unlink(_PS_SHIP_IMG_DIR_ . $id . '.jpg');
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . '/carrier_mini_' . $id . '.jpg')) {
            unlink(_PS_TMP_IMG_DIR_ . '/carrier_mini_' . $id . '.jpg');
        }
    }
}
