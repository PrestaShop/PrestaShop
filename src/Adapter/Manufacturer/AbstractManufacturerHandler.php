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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use Context;
use ImageManager;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Provides reusable methods for manufacturer command/query handlers
 */
abstract class AbstractManufacturerHandler extends AbstractObjectModelHandler
{
    /**
     * Validates that requested manufacturer was found
     *
     * @param ManufacturerId $manufacturerId
     * @param Manufacturer $manufacturer
     *
     * @throws ManufacturerNotFoundException
     */
    protected function assertManufacturerWasFound(ManufacturerId $manufacturerId, Manufacturer $manufacturer)
    {
        if ($manufacturer->id !== $manufacturerId->getValue()) {
            throw new ManufacturerNotFoundException(
                sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue())
            );
        }
    }

    /**
     * @param int $manufacturerId
     * @param string $newImagePath
     */
    protected function uploadImage($manufacturerId, $newImagePath)
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
            throw new ManufacturerImageUploadingException(
                'Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.',
                ManufacturerImageUploadingException::MEMORY_LIMIT_RESTRICTION
            );
        }
        // Copy new image
        if (!ImageManager::resize($temporaryImage, _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')) {
            throw new ManufacturerImageUploadingException(
                'An error occurred while uploading the image. Check your directory permissions.',
                ManufacturerImageUploadingException::UNEXPECTED_ERROR
            );
        }

        if (file_exists(_PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')) {
            $shopId = Context::getContext()->shop->id;
            $currentFile = _PS_TMP_IMG_DIR_ . 'manufacturer_mini_' . $manufacturerId . '_' . $shopId . '.jpg';

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }

        unlink($temporaryImage);
    }
}
