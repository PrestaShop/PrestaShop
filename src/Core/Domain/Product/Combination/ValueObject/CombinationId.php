<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;

/**
 *  Holds product combination identification data
 */
class CombinationId implements CombinationIdInterface
{
    /**
     * @var int
     */
    private $combinationId;

    /**
     * @param int $combinationId
     *
     * @throws CombinationConstraintException
     */
    public function __construct(int $combinationId)
    {
        $this->assertValueIsPositive($combinationId);
        $this->combinationId = $combinationId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->combinationId;
    }

    /**
     * @param int $value
     *
     * @throws CombinationConstraintException
     */
    private function assertValueIsPositive(int $value)
    {
        if (0 >= $value) {
            throw new CombinationConstraintException(
                sprintf('Combination id must be positive integer. "%s" given', $value),
                CombinationConstraintException::INVALID_ID
            );
        }
    }
}
