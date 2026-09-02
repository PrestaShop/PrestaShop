<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject;

/**
 * Indicates that no combination was specified
 */
class NoCombinationId implements CombinationIdInterface
{
    /**
     * Value when no combination is specified
     */
    public const NO_COMBINATION_ID = 0;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return self::NO_COMBINATION_ID;
    }
}
