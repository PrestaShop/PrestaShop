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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Category\QueryHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler\GetCategoriesTreeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;

/**
 * Handles @see GetCategoriesTree using legacy object model
 */
final class GetCategoriesTreeHandler implements GetCategoriesTreeHandlerInterface
{
    /**
     * @var string
     */
    private $contextLangId;

    /**
     * @param string $contextLangId
     */
    public function __construct(
        string $contextLangId
    ) {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoriesTree $query): array
    {
        $langId = $query->getLanguageId() ? $query->getLanguageId()->getValue() : (int) $this->contextLangId;
        $nestedCategories = Category::getNestedCategories(null, $langId, false);

        return $this->buildCategoriesTree($nestedCategories, $langId);
    }

    /**
     * @param array<string, array<string, mixed>> $categories
     * @param int $langId
     *
     * @return CategoryForTree[]
     */
    private function buildCategoriesTree(array $categories, int $langId): array
    {
        $categoriesTree = [];
        foreach ($categories as $category) {
            $categoryId = (int) $category['id_category'];
            $categoryActive = (bool) $category['active'];
            $categoryChildren = [];

            if (!empty($category['children'])) {
                $categoryChildren = $this->buildCategoriesTree($category['children'], $langId);
            }

            $categoriesTree[] = new CategoryForTree(
                $categoryId,
                $categoryActive,
                // @todo: it is always only one language now,
                //   but this way it doesn't require changing the contract when we want to allow retrieving multiple languages
                [$langId => $category['name']],
                $categoryChildren
            );
        }

        return $categoriesTree;
    }
}
