<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
