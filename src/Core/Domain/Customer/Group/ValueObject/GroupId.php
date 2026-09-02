<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;

/**
 * Holds group identification value
 */
class GroupId implements GroupIdInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $groupId
     */
    public function __construct(int $groupId)
    {
        $this->assertValueIsPositive($groupId);
        $this->value = $groupId;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    private function assertValueIsPositive(int $value): void
    {
        if (0 >= $value) {
            throw new GroupConstraintException(sprintf('Group id must be positive integer. "%s" given', $value), GroupConstraintException::INVALID_ID);
        }
    }
}
