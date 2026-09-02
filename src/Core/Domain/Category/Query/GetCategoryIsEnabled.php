<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Query;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Get current status (enabled/disabled) for given category.
 */
class GetCategoryIsEnabled
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @param int $categoryId
     */
    public function __construct($categoryId)
    {
        $this->categoryId = new CategoryId($categoryId);
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
