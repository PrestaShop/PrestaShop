<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Position\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Position\Exception\PositionConstraintException;

/**
 * This value object contains the necessary data to change a position.
 */
class RowPosition
{
    /**
     * @var int
     */
    private $rowId;

    /**
     * @var int
     */
    private $oldPosition;

    /**
     * @var int
     */
    private $newPosition;

    public function __construct(
        int $rowId,
        int $oldPosition,
        int $newPosition
    ) {
        if (0 >= $rowId) {
            throw new PositionConstraintException(
                sprintf('Row id %s is invalid. Row id must be number that is greater than zero.', var_export($rowId, true)),
                PositionConstraintException::INVALID_ROW_ID
            );
        }

        if (0 > $oldPosition) {
            throw new PositionConstraintException(
                sprintf('Old position %s is invalid. Old position must be number that is greater than zero.', var_export($rowId, true)),
                PositionConstraintException::INVALID_OLD_POSITION
            );
        }

        if (0 > $newPosition) {
            throw new PositionConstraintException(
                sprintf('New position %s is invalid. New position must be number that is greater than zero.', var_export($rowId, true)),
                PositionConstraintException::INVALID_NEW_POSITION
            );
        }

        $this->rowId = $rowId;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return int
     */
    public function getRowId(): int
    {
        return $this->rowId;
    }

    /**
     * @return int
     */
    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    /**
     * @return int
     */
    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
