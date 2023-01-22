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

namespace Tests\Resources\Resetter;

use Shop;
use Tests\Resources\DatabaseDump;

class ShopResetter
{
    public static function resetShops(): void
    {
        DatabaseDump::restoreTables([
            // Configuration also needs to be restored since it contains the multishop configuration
            'configuration',
            'shop',
            'shop_group',
            'shop_url',
        ]);
        DatabaseDump::restoreMatchingTables('/.*_shop$/');

        // We need to restore lang tables that are also multi-shop
        DatabaseDump::restoreTables([
            'carrier_lang',
            'category_lang',
            'cms_category_lang',
            'cms_lang',
            'cms_role_lang',
            'customization_field_lang',
            'info_lang',
            'linksmenutop_lang',
            'meta_lang',
            'product_lang',
        ]);
        Shop::resetContext();
    }
}
