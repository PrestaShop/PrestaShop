<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string The HTML < img > tag
     */
    public function getThumbnailForListing($imageId, $imageType = 'jpg', $tableName = 'product', $imageDir = _PS_PRODUCT_IMG_DIR_)
    {
        $thumbPath = $this->getThumbnailTag($imageId, $imageType, $tableName, $imageDir);

        // because legacy uses relative path to reach a directory under root directory...
        $replacement = 'src="' . $this->legacyContext->getRootUrl();
        $thumbPath = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $thumbPath);

        return $thumbPath;
    }

    /**
     * @param int $imageId
     *
     * @return string
     */
    public function getThumbnailPath($imageId)
    {
        $imageType = 'jpg';
        $tableName = 'product';
        $imageDir = _PS_PRODUCT_IMG_DIR_;

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
     * @param int $imageId
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
     * @param int $imageId
     * @param string $imageType
     * @param string $tableName
     * @param string $imageDir
     *
     * @return string
     */
    private function getImagePath($imageId, $imageType, $tableName, $imageDir)
    {
        if ($tableName == 'product') {
            $image = new Image($imageId);

            return $imageDir . $image->getExistingImgPath() . '.' . $imageType;
        }

        return $imageDir . $imageId . '.' . $imageType;
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
        return $tableName . '_mini_' . $imageId . '.' . $imageType;
    }
}
