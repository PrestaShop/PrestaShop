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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Position\ValueObject\RowPosition;

/**
 * Updates product details
 */
class UpdateProductsPositionsCommand
{
    /**
     * List of the positions data for the grid:
     * $positions = [
     *     [
     *         'rowId' => 42,
     *         'oldPosition' => 1,
     *         'oldPosition' => 3,
     *     ],
     *     [
     *         'rowId' => 43,
     *         'oldPosition' => 2,
     *         'oldPosition' => 2,
     *     ],
     *     [
     *         'rowId' => 44,
     *         'oldPosition' => 3,
     *         'oldPosition' => 1,
     *     ],
     * ];
     *
     * @var array
     */
    private $positions;

    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * UpdateProductPositionCommand constructor.
     *
     * @param array $positions
     * @param int $categoryId
     */
    public function __construct(array $positions, int $categoryId)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->setPositions($positions);
    }

    /**
     * @return RowPosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    private function setPositions(array $positions): void
    {
        $this->positions = array_map(static function (array $position): RowPosition {
            // We use -1 as the default fallback because it's not a valid value in the VO the idea is to trigger
            // an exception via the VO when the field is not specified.
            return new RowPosition(
                (int) ($position['rowId'] ?? -1),
                (int) ($position['oldPosition'] ?? -1),
                (int) ($position['newPosition'] ?? -1)
            );
        }, $positions);
    }
}
