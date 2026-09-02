<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookConstraintException;

/**
 * Hook identity.
 */
class HookId
{
    /**
     * @var int
     */
    private $hookId;

    /**
     * @param int $hookId
     */
    public function __construct($hookId)
    {
        $this->assertIntegerIsGreaterThanZero($hookId);

        $this->hookId = $hookId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->hookId;
    }

    /**
     * @param int $hookId
     */
    private function assertIntegerIsGreaterThanZero($hookId)
    {
        if (!is_int($hookId) || 0 > $hookId) {
            throw new HookConstraintException(sprintf('Hook id %d is invalid. Hook id must be number that is greater than zero.', var_export($hookId, true)));
        }
    }
}
