<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
            throw new CategoryConstraintException(
                sprintf('Category status %s is invalid. Status must be of type "bool".', var_export($newStatus, true)),
                CategoryConstraintException::INVALID_STATUS
            );
        }

        $this->newStatus = $newStatus;

        return $this;
    }
}
