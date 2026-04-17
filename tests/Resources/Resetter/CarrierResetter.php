<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class CarrierResetter
{
    public static function resetCarrier(): void
    {
        DatabaseDump::restoreTables([
            'carrier',
            'carrier_group',
            'carrier_lang',
            'carrier_shop',
            'carrier_tax_rules_group_shop',
            'carrier_zone',
            'range_price',
            'range_weight',
            'delivery',
        ]);
    }
}
