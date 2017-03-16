<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

/**
 * TODO
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
     * @return string The HTML < img > tag
     */
    public function getThumbnailForListing($imageId, $imageType = 'jpg', $tableName = 'product', $imageDir = 'p')
    {
        if ($tableName == 'product') {
            $image = new \Image($imageId);
            $path_to_image = _PS_IMG_DIR_.$imageDir.'/'.$image->getExistingImgPath().'.'.$imageType;
        } else {
            $path_to_image = _PS_IMG_DIR_.$imageDir.'/'.$imageId.'.'.$imageType;
        }
        $thumbPath = \ImageManager::thumbnail($path_to_image, $tableName.'_mini_'.$imageId.'.'.$imageType, 45, $imageType);

        // because legacy uses relative path to reach a directory under root directory...
        $replacement = 'src="'.$this->legacyContext->getRootUrl();
        $thumbPath = preg_replace('/src="(\\.\\.\\/)+/', $replacement, $thumbPath);

        return $thumbPath;
    }
}
