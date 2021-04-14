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

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use Image;

class ProductImagePathFactory
{
    public const IMAGE_TYPE_SMALL_DEFAULT = 'small_default';
    public const IMAGE_TYPE_MEDIUM_DEFAULT = 'medium_default';
    public const IMAGE_TYPE_LARGE_DEFAULT = 'large_default';
    public const IMAGE_TYPE_HOME_DEFAULT = 'home_default';
    public const IMAGE_TYPE_CART_DEFAULT = 'cart_default';

    /**
     * @var bool
     */
    private $isLegacyImageMode;

    /**
     * @var string
     */
    private $temporaryImgDir;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $pathToBaseDir;

    /**
     * @var string
     */
    private $contextLangIsoCode;

    /**
     * @param bool $isLegacyImageMode
     * @param string $pathToBaseDir
     * @param string $temporaryImgDir
     * @param string $contextLangIsoCode
     */
    public function __construct(
        bool $isLegacyImageMode,
        string $pathToBaseDir,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ) {
        $this->isLegacyImageMode = $isLegacyImageMode;
        // make sure one trailing slash is always there
        $this->temporaryImgDir = rtrim($temporaryImgDir, '/') . '/';
        $this->pathToBaseDir = rtrim($pathToBaseDir, '/') . '/';
        $this->contextLangIsoCode = $contextLangIsoCode;
    }

    /**
     * @param Image $image
     *
     * @return string
     */
    public function getBaseImagePath(Image $image): string
    {
        $path = $this->getBaseImagePathWithoutExtension($image);

        return sprintf('%s.%s', $path, $image->image_format);
    }

    /**
     * @param Image $image
     * @param string $type
     *
     * @return string
     */
    public function getPathByType(Image $image, string $type): string
    {
        $path = $this->getBaseImagePathWithoutExtension($image);

        return sprintf('%s-%s.%s', $path, $type, $image->image_format);
    }

    /**
     * @param string $type
     * @param string|null $langIso
     *
     * @return string
     */
    public function getNoImagePath(string $type, ?string $langIso = null): string
    {
        if (!$langIso) {
            $langIso = $this->contextLangIsoCode;
        }

        return sprintf('%s%s-%s-%s.jpg', $this->pathToBaseDir, $langIso, 'default', $type);
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

    /**
     * @param Image $image
     *
     * @return string
     */
    private function getBaseImagePathWithoutExtension(Image $image): string
    {
        if ($this->isLegacyImageMode) {
            $path = $image->id_product . '-' . $image->id;
        } else {
            $path = ltrim($image->getImgPath(), '/');
        }

        return sprintf('%s%s', $this->pathToBaseDir, $path);
    }
}
