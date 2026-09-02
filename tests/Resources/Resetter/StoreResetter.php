<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class StoreResetter
{
    public static function resetStores(): void
    {
        DatabaseDump::restoreTables([
            'store',
            'store_lang',
            'store_shop',
        ]);
    }
}
