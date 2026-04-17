<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Combination\Generator;

use Iterator;

/**
 * Responsible for generating all possible combinations of product attribute ids
 */
interface CombinationGeneratorInterface
{
    /**
     * @param array[] $attributesByGroups
     *
     * @return Iterator
     */
    public function generate(array $attributesByGroups): Iterator;
}
