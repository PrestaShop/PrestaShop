<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Combination as CombinationLegacy;

/**
 * Adapter for Combination legacy class.
 */
class Combination
{
    /**
     * Check if a combination exists in database.
     *
     * @param int $combinationId
     *
     * @return bool
     */
    public function existsInDatabase(int $combinationId): bool
    {
        return CombinationLegacy::existsInDatabase($combinationId);
    }
}
