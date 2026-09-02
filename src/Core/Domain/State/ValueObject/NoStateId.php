<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\ValueObject;

/**
 * Provides state id
 */
class NoStateId implements StateIdInterface
{
    /**
     * @var int
     */
    public const NO_STATE_ID_VALUE = 0;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return self::NO_STATE_ID_VALUE;
    }
}
