<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject;

/**
 * Defines contract for combination identity value
 */
interface CombinationIdInterface
{
    /**
     * @return int
     */
    public function getValue(): int;
}
