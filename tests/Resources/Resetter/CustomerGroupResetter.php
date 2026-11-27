<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class CustomerGroupResetter
{
    /**
     * Restores the group tables and every table Group::add() and Group::delete() write to.
     */
    public static function resetCustomerGroups(): void
    {
        DatabaseDump::restoreTables([
            'carrier_group',
            'cart_rule',
            'cart_rule_group',
            'category_group',
            'configuration',
            'customer',
            'customer_group',
            'group',
            'group_lang',
            'group_reduction',
            'group_shop',
            'module_group',
            'product_group_reduction_cache',
            'specific_price',
            'specific_price_rule',
        ]);
    }
}
