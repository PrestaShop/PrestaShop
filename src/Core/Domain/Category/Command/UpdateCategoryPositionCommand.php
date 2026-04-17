<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Updates category position
 */
class UpdateCategoryPositionCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var CategoryId
     */
    private $parentCategoryId;

    /**
     * @var int
     */
    private $way;

    /**
     * @var array
     */
    private $positions;

    /**
     * @var bool
     */
    private $foundFirst;

    /**
     * @param int $categoryId
     * @param int $parentCategoryId
     * @param int $way
     * @param array $positions
     * @param bool $foundFirst
     */
    public function __construct($categoryId, $parentCategoryId, $way, array $positions, $foundFirst)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->parentCategoryId = new CategoryId($parentCategoryId);
        $this->way = $way;
        $this->positions = $positions;
        $this->foundFirst = $foundFirst;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return CategoryId
     */
    public function getParentCategoryId()
    {
        return $this->parentCategoryId;
    }

    /**
     * @return int
     */
    public function getWay()
    {
        return $this->way;
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @return bool
     */
    public function isFoundFirst()
    {
        return $this->foundFirst;
    }
}
