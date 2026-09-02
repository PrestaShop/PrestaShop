<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception;

class CannotGenerateCombinationException extends CombinationException
{
    public const DIFFERENT_ATTRIBUTES_BETWEEN_SHOPS = 1;
}
