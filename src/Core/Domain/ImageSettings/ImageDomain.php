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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings;

use RuntimeException;

enum ImageDomain: string
{
    case ALL = 'all';
    case CATEGORIES = 'categories';
    case MANUFACTURERS = 'manufacturers';
    case SUPPLIERS = 'suppliers';
    case PRODUCTS = 'products';
    case STORES = 'stores';

    /**
     * Get the directory path for the image type
     */
    public function getDirectory(): string
    {
        return match ($this) {
            self::CATEGORIES => _PS_CAT_IMG_DIR_,
            self::MANUFACTURERS => _PS_MANU_IMG_DIR_,
            self::SUPPLIERS => _PS_SUPP_IMG_DIR_,
            self::PRODUCTS => _PS_PRODUCT_IMG_DIR_,
            self::STORES => _PS_STORE_IMG_DIR_,
            self::ALL => throw new RuntimeException("getDirectory() is not usable for 'ALL' image domain"),
        };
    }

    public function isProduct(): bool
    {
        return $this === self::PRODUCTS;
    }

    /**
     * Get all image types with thumbnails
     *
     * @return array<self>
     */
    public static function getDomainsWithThumbnails(): array
    {
        return array_filter(self::cases(), static fn (self $imageType) => $imageType !== self::ALL);
    }

    public static function getAllowedValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
