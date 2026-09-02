<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class FeatureFlagResetter
{
    public static function resetFeatureFlags(): void
    {
        DatabaseDump::restoreTables([
            'feature_flag',
        ]);
    }
}
