<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Category\QueryHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\QueryHandler\GetCategoryForEditingHandlerInterface;

/**
 * Class GetCategoryForEditingHandler
 */
final class GetCategoryForEditingHandler implements GetCategoryForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoryForEditing $query)
    {
        $category = new Category($query->getCategoryId()->getValue());

        if (!$category->id) {
            throw new CategoryNotFoundException(
                sprintf('Category with id "%s" was not found', $query->getCategoryId()->getValue())
            );
        }

        $editableCategory = new EditableCategory(
            $category->name,
            (bool) $category->active,
            $category->description,
            $category->id_parent,
            $category->meta_title,
            $category->meta_description,
            $category->meta_keywords,
            $category->link_rewrite,
            $category->getGroups(),
            $category->getAssociatedShops()
        );

        return $editableCategory;
    }
}
