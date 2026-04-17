<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Product\Combination\Generator;

use Iterator;

/**
 * Generates combinations of attributes
 */
class CombinationGenerator implements CombinationGeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * Yields combinations, where each combination contains a list of all possible attribute ids indexed by group id
     */
    public function generate(array $attributesByGroups): Iterator
    {
        $currentGroup = key($attributesByGroups);
        $currentAttributes = $attributesByGroups[$currentGroup];
        unset($attributesByGroups[$currentGroup]);

        foreach ($currentAttributes as $attributeId) {
            if (empty($attributesByGroups)) {
                yield [$currentGroup => $attributeId];

                continue;
            }

            foreach ($this->generate($attributesByGroups) as $combination) {
                $combination[$currentGroup] = $attributeId;

                yield array_reverse($combination, true);
            }
        }
    }
}
