<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;

/**
 * Class CategoryId.
 */
class CategoryId
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @param int $categoryId
     */
    public function __construct($categoryId)
    {
        $this->setCategoryId($categoryId);
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->categoryId;
    }

    /**
     * @param CategoryId $categoryId
     *
     * @return bool
     */
    public function isEqual(CategoryId $categoryId)
    {
        return $this->getValue() === $categoryId->getValue();
    }

    /**
     * @param int $categoryId
     */
    private function setCategoryId($categoryId)
    {
        if (!is_int($categoryId) || 0 >= $categoryId) {
            throw new CategoryException(sprintf('Invalid Category id %s supplied', var_export($categoryId, true)));
        }

        $this->categoryId = $categoryId;
    }
}
