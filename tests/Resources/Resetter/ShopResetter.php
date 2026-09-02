<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            'meta_lang',
            'product_lang',
        ]);
        Shop::resetContext();
    }
}
