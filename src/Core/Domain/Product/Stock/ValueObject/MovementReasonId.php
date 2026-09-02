<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConstraintException;

/**
 * Stock movement reason identifier.
 */
class MovementReasonId
{
    /**
     * Configuration keys for mapping MovementReason to id.
     *
     * @todo: add other keys
     */
    public const INCREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY = 'PS_STOCK_MVT_INC_EMPLOYEE_EDITION';
    public const DECREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY = 'PS_STOCK_MVT_DEC_EMPLOYEE_EDITION';

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $movementReasonId
     *
     * @throws MovementReasonConstraintException
     */
    public function __construct(int $movementReasonId)
    {
        $this->assertIsGreaterThanZero($movementReasonId);

        $this->value = $movementReasonId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    private function assertIsGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new MovementReasonConstraintException(
                sprintf('Stock MovementReasonId %s is invalid.', $value),
                MovementReasonConstraintException::INVALID_ID
            );
        }
    }
}
