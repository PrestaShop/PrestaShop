<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject;

/**
 * Indicates that no group is specified
 */
class NoGroupId implements GroupIdInterface
{
    /**
     * Value when no group is specified
     */
    public const NO_GROUP_ID = 0;

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return self::NO_GROUP_ID;
    }
}
