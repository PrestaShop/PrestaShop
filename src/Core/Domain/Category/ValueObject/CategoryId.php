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
