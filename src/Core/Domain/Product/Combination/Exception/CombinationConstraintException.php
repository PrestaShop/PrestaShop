<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception;

/**
 * Thrown when constraints specific to Combination are violated
 */
class CombinationConstraintException extends CombinationException
{
    /**
     * When combination id is invalid
     */
    public const INVALID_ID = 1;
}
