<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class ApiClientResetter
{
    public static function resetApiClient(): void
    {
        DatabaseDump::restoreTables(['api_client']);
    }
}
