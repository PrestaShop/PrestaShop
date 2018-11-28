<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter;

use Image;
use ImageManager as LegacyImageManager;

/**
 * Class responsible of finding images and creating thumbnails.
 */
class ImageManager
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * Old legacy way to generate a thumbnail.
     *
     * Use it upon a new Image management system is available.
     *
     * @param $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string The HTML < img > tag
     */
    public function getThumbnailForListing($imageId, $imageType = 'jpg', $tableName = 'product', $imageDir = 'p')
    {
        $thumbPath = $this->getThumbnailTag($imageId, $imageType, $tableName, $imageDir);

        // because legacy uses relative path to reach a directory under root directory...
        $replacement = 'src="' . $this->legacyContext->getRootUrl();
        $thumbPath = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $thumbPath);

        return $thumbPath;
    }

    public function getThumbnailPath($imageId)
    {
        $imageType = 'jpg';
        $tableName = 'product';
        $imageDir = 'p';

        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);
        $thumbnailCachedImageName = $this->makeCachedImageName($imageId, $imageType, $tableName);
        LegacyImageManager::thumbnail(
            $imagePath,
            $thumbnailCachedImageName,
            45,
            $imageType
        );

        return LegacyImageManager::getThumbnailPath($thumbnailCachedImageName, false);
    }

    /**
     * @param $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string
     */
    private function getThumbnailTag($imageId, $imageType, $tableName, $imageDir)
    {
        $imagePath = $this->getImagePath($imageId, $imageType, $tableName, $imageDir);

        return LegacyImageManager::thumbnail(
            $imagePath,
            $this->makeCachedImageName($imageId, $imageType, $tableName),
            45,
            $imageType
        );
    }

    /**
     * @param $imageId
     * @param $imageType
     * @param $tableName
     * @param $imageDir
     *
     * @return string
     */
    private function getImagePath($imageId, $imageType, $tableName, $imageDir)
    {
        $parentDirectory = _PS_IMG_DIR_ . $imageDir;

        if ($tableName == 'product') {
            $image = new Image($imageId);

            return $parentDirectory . '/' . $image->getExistingImgPath() . '.' . $imageType;
        }

        return $parentDirectory . '/' . $imageId . '.' . $imageType;
    }

    /**
     * @param $imageId
     * @param $imageType
     * @param $tableName
     *
     * @return string
     */
    private function makeCachedImageName($imageId, $imageType, $tableName)
    {
        return $tableName . '_mini_' . $imageId . '.' . $imageType;
    }
}
