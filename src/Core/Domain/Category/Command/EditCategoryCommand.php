<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class EditCategoryCommand edits given category.
 */
class EditCategoryCommand extends AbstractCategoryCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var int
     */
    private $parentCategoryId;

    /**
     * @param CategoryId $categoryId
     */
    public function __construct(CategoryId $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return int
     */
    public function getParentCategoryId()
    {
        return $this->parentCategoryId;
    }

    /**
     * @param int $parentCategoryId
     *
     * @return self
     *
     * @throws CategoryConstraintException
     */
    public function setParentCategoryId($parentCategoryId)
    {
        if (!is_int($parentCategoryId) || 0 >= $parentCategoryId) {
            throw new CategoryConstraintException(
                sprintf('Invalid Category parent id %s supplied', var_export($parentCategoryId, true)),
                CategoryConstraintException::INVALID_PARENT_ID
            );
        }

        if ($this->categoryId->isEqual(new CategoryId($parentCategoryId))) {
            throw new CategoryConstraintException('Category cannot be parent of itself.');
        }

        $this->parentCategoryId = $parentCategoryId;

        return $this;
    }
}
