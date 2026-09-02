<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class DeleteCategoryCommand deletes provided category.
 */
class DeleteCategoryCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var CategoryDeleteMode
     */
    private $deleteMode;

    /**
     * @param int $categoryId
     * @param string $mode
     */
    public function __construct($categoryId, $mode)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->deleteMode = new CategoryDeleteMode($mode);
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return CategoryDeleteMode
     */
    public function getDeleteMode()
    {
        return $this->deleteMode;
    }
}
