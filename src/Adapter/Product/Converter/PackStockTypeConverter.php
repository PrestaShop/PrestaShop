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

namespace PrestaShop\PrestaShop\Adapter\Product\Converter;

use Pack as LegacyPack;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;

class PackStockTypeConverter
{
    /**
     * @param string $packStockType
     *
     * @return int
     */
    public static function convertToLegacy(string $packStockType): int
    {
        switch ($packStockType) {
            case PackStockType::STOCK_TYPE_PACK_ONLY:
                return LegacyPack::STOCK_TYPE_PACK_ONLY;
            case PackStockType::STOCK_TYPE_PRODUCTS_ONLY:
                return LegacyPack::STOCK_TYPE_PRODUCTS_ONLY;
            case PackStockType::STOCK_TYPE_BOTH:
                return LegacyPack::STOCK_TYPE_PACK_BOTH;
            case PackStockType::STOCK_TYPE_DEFAULT:
            default:
                return LegacyPack::STOCK_TYPE_DEFAULT;
        }
    }

    /**
     * @param int $legacyPackStockType
     *
     * @return string
     */
    public static function convertToValueObject(int $legacyPackStockType): string
    {
        switch ($legacyPackStockType) {
            case LegacyPack::STOCK_TYPE_PACK_ONLY:
                return PackStockType::STOCK_TYPE_PACK_ONLY;
            case LegacyPack::STOCK_TYPE_PRODUCTS_ONLY:
                return PackStockType::STOCK_TYPE_PRODUCTS_ONLY;
            case LegacyPack::STOCK_TYPE_PACK_BOTH:
                return PackStockType::STOCK_TYPE_BOTH;
            case LegacyPack::STOCK_TYPE_DEFAULT:
            default:
                return PackStockType::STOCK_TYPE_DEFAULT;
        }
    }
}
