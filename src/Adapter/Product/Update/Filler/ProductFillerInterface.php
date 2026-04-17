<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

/**
 * Responsible for filling up the Product with the properties which have to be updated
 */
interface ProductFillerInterface
{
    /**
     * Fill product properties from the command and return an array of the properties to update.
     *
     * Returns a list of properties that were filled.
     * Simple (not multilingual) fields will be provided in a simple array as a values, while for
     * multilingual ones the array key will be the field name and the value will be an array of language ids.
     *
     * @return array<int|string, string|int[]>
     *
     * e.g.:
     * [
     *     'reference',
     *     'visibility',
     *     'name' => [1, 2],
     * ]
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array;
}
