<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

/**
 * Responsible for filling up the Combination with the properties which have to be updated
 */
interface CombinationFillerInterface
{
    /**
     * Fill combination properties from the command and return an array of the properties to update.
     *
     * Returns a list of properties that were filled.
     * Simple (not multilingual) fields will be provided in a simple array as a values, while for
     * multilingual ones the array key will be the field name and the value will be an array of language ids.
     *
     * @return array<int, string|array<string, int>>
     *
     * e.g.:
     * [
     *     'reference',
     *     'visibility',
     *     'name' => [1, 2],
     * ]
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array;
}
