<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
