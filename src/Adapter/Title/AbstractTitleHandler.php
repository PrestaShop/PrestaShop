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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title;

use Context;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Title\Repository\TitleRepository;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleImageUploadingException;

class AbstractTitleHandler
{
    /**
     * @var TitleRepository
     */
    protected $titleRepository;

    /**
     * @param TitleRepository $titleRepository
     */
    public function __construct(TitleRepository $titleRepository)
    {
        $this->titleRepository = $titleRepository;
    }

    /**
     * @param int $titleId
     * @param string $newImagePath
     * @param string $imageDir
     * @param int $imageWidth
     * @param int $imageHeight
     */
    protected function uploadImage(
        int $titleId,
        string $newImagePath,
        string $imageDir,
        int $imageWidth,
        int $imageHeight
    ): void {
        $temporaryImage = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        if (!$temporaryImage) {
            return;
        }

        if (!copy($newImagePath, $temporaryImage)) {
            return;
        }

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($temporaryImage)) {
            throw new TitleImageUploadingException(
                'Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.',
                TitleImageUploadingException::MEMORY_LIMIT_RESTRICTION
            );
        }

        // Copy new image
        if (!ImageManager::resize(
            $temporaryImage,
            _PS_IMG_DIR_ . $imageDir . $titleId . '.jpg',
            $imageWidth,
            $imageHeight
        )) {
            throw new TitleImageUploadingException(
                'An error occurred while uploading the image. Check your directory permissions.',
                TitleImageUploadingException::UNEXPECTED_ERROR
            );
        }

        if (file_exists(_PS_GENDERS_DIR_ . $titleId . '.jpg')) {
            $shopId = Context::getContext()->shop->id;
            $currentFile = _PS_TMP_IMG_DIR_ . 'lang_mini_' . $titleId . '_' . $shopId . '.jpg';

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }

        unlink($newImagePath);
        unlink($temporaryImage);
    }
}
