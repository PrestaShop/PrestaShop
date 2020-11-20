<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

use Image;

class ProductImagePathFactory
{
    /**
     * @var bool
     */
    private $isLegacyImageMode;

    /**
     * @var string
     */
    private $temporaryImgDir;

    /**
     * @param bool $isLegacyImageMode
     * @param string $temporaryImgDir
     */
    public function __construct(
        bool $isLegacyImageMode,
        string $temporaryImgDir
    ) {
        $this->isLegacyImageMode = $isLegacyImageMode;
        $this->temporaryImgDir = $temporaryImgDir;
    }

    /**
     * @param Image $image
     *
     * @return string
     */
    public function getBasePath(Image $image): string
    {
        if ($this->isLegacyImageMode) {
            $path = $image->id_product . '-' . $image->id;
        } else {
            $path = $image->getImgPath();
        }

        return sprintf('%s%s.%s', _PS_PROD_IMG_DIR_, $path, $image->image_format);
    }

    /**
     * @param int $productId
     *
     * @return string
     */
    public function getCachedCover(int $productId): string
    {
        return sprintf('%sproduct_%d.jpg', $this->temporaryImgDir, $productId);
    }

    /**
     * @param int $productId
     * @param int $shopId
     *
     * @return string
     */
    public function getHelperThumbnail(int $productId, int $shopId): string
    {
        return sprintf('%sproduct_mini_%d_%d.jpg', $this->temporaryImgDir, $productId, $shopId);
    }
}
