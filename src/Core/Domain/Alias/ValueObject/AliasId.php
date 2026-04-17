<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;

/**
 * Defines Alias ID with it's constraints.
 */
class AliasId
{
    /**
     * @var int
     */
    private $aliasId;

    /**
     * @param int $aliasId
     *
     * @throws AliasConstraintException
     */
    public function __construct(int $aliasId)
    {
        $this->assertIntegerIsGreaterThanZero($aliasId);

        $this->aliasId = $aliasId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->aliasId;
    }

    /**
     * @param int $aliasId
     *
     * @throws AliasConstraintException
     */
    private function assertIntegerIsGreaterThanZero(int $aliasId)
    {
        if (0 >= $aliasId) {
            throw new AliasConstraintException(
                sprintf('Invalid alias id %d supplied. Alias id must be a positive integer.', $aliasId),
                AliasConstraintException::INVALID_ID
            );
        }
    }
}
