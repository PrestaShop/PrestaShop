<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
