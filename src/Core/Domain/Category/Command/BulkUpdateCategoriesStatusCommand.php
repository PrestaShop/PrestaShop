<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Updates provided categories to new status
 */
class BulkUpdateCategoriesStatusCommand
{
    /**
     * @var CategoryId[]
     */
    private $categoryIds;

    /**
     * @var bool
     */
    private $newStatus;

    /**
     * @param int[] $categoryIds
     * @param bool $newStatus
     *
     * @throws CategoryConstraintException
     * @throws CategoryException
     */
    public function __construct(array $categoryIds, $newStatus)
    {
        $this
            ->setCategoryIds($categoryIds)
            ->setNewStatus($newStatus)
        ;
    }

    /**
     * @return CategoryId[]
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @return bool
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * @param int[] $categoryIds
     *
     * @return self
     *
     * @throws CategoryConstraintException
     * @throws CategoryException
     */
    private function setCategoryIds(array $categoryIds)
    {
        if (empty($categoryIds)) {
            throw new CategoryConstraintException('Missing categories data for status change');
        }

        foreach ($categoryIds as $categoryId) {
            $this->categoryIds[] = new CategoryId((int) $categoryId);
        }

        return $this;
    }

    /**
     * @param bool $newStatus
     *
     * @return self
     */
    private function setNewStatus($newStatus)
    {
        if (!is_bool($newStatus)) {
            throw new CategoryConstraintException(sprintf('Category status %s is invalid. Status must be of type "bool".', var_export($newStatus, true)), CategoryConstraintException::INVALID_STATUS);
        }

        $this->newStatus = $newStatus;

        return $this;
    }
}
