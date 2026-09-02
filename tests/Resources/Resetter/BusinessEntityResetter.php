<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class BusinessEntityResetter
{
    public static function resetBusinessEntities(): void
    {
        DatabaseDump::restoreTables([
            'address',
            'business_entity',
            'business_entity_address',
            'business_entity_customer_b2b',
            'business_entity_identifier',
        ]);
    }
}
