<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class BulkDeleteCategoriesCommand.
 */
class BulkDeleteCategoriesCommand
{
    /**
     * @var CategoryId[]
     */
    private $categoryIds;

    /**
     * @var CategoryDeleteMode
     */
    private $deleteMode;

    /**
     * @param int[] $categoryIds
     * @param string $deleteMode
     *
     * @throws CategoryConstraintException
     * @throws CategoryException
     */
    public function __construct(array $categoryIds, $deleteMode)
    {
        $this
            ->setCategoryIds($categoryIds)
            ->setDeleteMode($deleteMode)
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
     * @return CategoryDeleteMode
     */
    public function getDeleteMode()
    {
        return $this->deleteMode;
    }

    /**
     * @param string $mode
     *
     * @return self
     */
    private function setDeleteMode($mode)
    {
        $this->deleteMode = new CategoryDeleteMode($mode);

        return $this;
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
            throw new CategoryConstraintException('Missing Category data for bulk deleting', CategoryConstraintException::EMPTY_BULK_DELETE_DATA);
        }

        foreach ($categoryIds as $categoryId) {
            $this->categoryIds[] = new CategoryId((int) $categoryId);
        }

        return $this;
    }
}
