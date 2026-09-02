<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
