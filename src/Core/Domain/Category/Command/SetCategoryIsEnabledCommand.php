<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class ToggleCategoryStatusCommand toggles given category status.
 */
class SetCategoryIsEnabledCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @param int $categoryId
     * @param bool $isEnabled
     */
    public function __construct($categoryId, $isEnabled)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }
}
