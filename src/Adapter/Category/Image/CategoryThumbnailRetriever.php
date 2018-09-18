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

namespace PrestaShop\PrestaShop\Adapter\Category\Image;

use ImageManager;

class CategoryThumbnailRetriever
{
    /**
     * @param int $categoryId
     *
     * @return string
     */
    public function retrieve($categoryId)
    {
        $imageType = 'jpg';
        $tableName = 'category';
        $imageDir = 'c';
        $imagePath = $this->getImagePath($categoryId, $imageType, $imageDir);

        $thumbnailCachedImageName = $this->makeCachedImageName($categoryId, $imageType, $tableName);
        ImageManager::thumbnail(
            $imagePath,
            $thumbnailCachedImageName,
            45,
            $imageType
        );

        return ImageManager::getThumbnailPath($thumbnailCachedImageName, false);
    }

    /**
     * @param int $imageId
     * @param string $imageType
     * @param string $imageDir
     *
     * @return string
     */
    private function getImagePath($imageId, $imageType, $imageDir)
    {
        $parentDirectory = _PS_IMG_DIR_ . $imageDir;

        return $parentDirectory . '/' . $imageId . '.' . $imageType;
    }

    /**
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     *
     * @return string
     */
    private function makeCachedImageName($imageId, $imageType, $tableName)
    {
        return $tableName . '_' . $imageId . '.' . $imageType;
    }
}
