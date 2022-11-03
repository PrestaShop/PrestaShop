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

namespace Tests\Unit\Core\Domain\Position\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Position\Exception\PositionConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Position\ValueObject\RowPosition;

class RowPositionTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $rowId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function testItIsSuccessfullyConstructed(int $rowId, int $oldPosition, int $newPosition): void
    {
        $rowPosition = new RowPosition($rowId, $oldPosition, $newPosition);
        $this->assertEquals($rowId, $rowPosition->getRowId());
        $this->assertEquals($oldPosition, $rowPosition->getOldPosition());
        $this->assertEquals($newPosition, $rowPosition->getNewPosition());
    }

    public function getValidValues(): iterable
    {
        yield [1, 1, 2];
        yield [1, 3, 2];
        yield [23, 0, 1];
        yield [45, 1, 0];
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $rowId
     * @param int $oldPosition
     * @param int $newPosition
     * @param int $expectedErrorCode
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $rowId, int $oldPosition, int $newPosition, int $expectedErrorCode): void
    {
        $this->expectException(PositionConstraintException::class);
        $this->expectExceptionCode($expectedErrorCode);

        new RowPosition($rowId, $oldPosition, $newPosition);
    }

    public function getInvalidValues(): iterable
    {
        yield [0, 1, 2, PositionConstraintException::INVALID_ROW_ID];
        yield [-1, 1, 2, PositionConstraintException::INVALID_ROW_ID];
        yield [23, -1, 1, PositionConstraintException::INVALID_OLD_POSITION];
        yield [23, 1, -1, PositionConstraintException::INVALID_NEW_POSITION];
    }
}
