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
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler\GetCategoriesTreeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var string[]
     */
    private $duplicateCategoryNames = [];

    /**
     * @param string $contextLangId
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        string $contextLangId,
        CategoryRepository $categoryRepository
    ) {
        $this->contextLangId = $contextLangId;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoriesTree $query): array
    {
        $langId = $query->getLanguageId() ? $query->getLanguageId()->getValue() : (int) $this->contextLangId;
        $nestedCategories = Category::getNestedCategories(null, $langId, false);
        //@todo; hardcoded shop id. Should I add shop constraint to query, or take context shop id?
        $this->duplicateCategoryNames = $this->categoryRepository->getDuplicateNames(new ShopId(1), $query->getLanguageId());

        return $this->buildCategoriesTree($nestedCategories, $langId);
    }

    /**
     * @param array<string, array<string, mixed>> $categories
     * @param int $langId
     * @param array<string, array<string, mixed>> $parents
     *
     * @return CategoryForTree[]
     */
    private function buildCategoriesTree(array $categories, int $langId, array $parents = []): array
    {
        $categoriesTree = [];
        foreach ($categories as $category) {
            $categoryId = (int) $category['id_category'];
            $categoryActive = (bool) $category['active'];
            $categoryChildren = [];

            if (!empty($category['children'])) {
                $categoryChildren = $this->buildCategoriesTree(
                    $category['children'],
                    $langId,
                    array_merge($parents, [$category])
                );
            }

            $categoriesTree[] = new CategoryForTree(
                $categoryId,
                $categoryActive,
                $this->buildDisplayName($category, $parents),
                // @todo: it is always only one language now,
                //   but this way it doesn't require changing the contract when we want to allow retrieving multiple languages
                [$langId => $category['name']],
                $categoryChildren
            );
        }

        return $categoriesTree;
    }

    /**
     * If there are multiple categories with identical names, we want to be able to tell them apart,
     * so we use breadcrumb path instead of category name.
     * However, whole breadcrumb path would probably be too long, therefore not UX friendly.
     * Calculating "optimal" breadcrumb length seems too complex compared to the value it could bring.
     * So, we show one parent name and category name, as it is simple and should cover most cases.
     *
     * e.g. "Clothes > Women"
     *
     * @param array<string, mixed> $category
     * @param array<int, array<string, mixed>> $parentCategories
     *
     * @return string
     */
    private function buildDisplayName(array $category, array $parentCategories): string
    {
        $categoryName = $category['name'];

        if (!in_array($categoryName, $this->duplicateCategoryNames)) {
            return $categoryName;
        }

        $breadcrumbs = [];
        foreach ($parentCategories as $parent) {
            $breadcrumbs[] = $parent['name'];
        }
        $breadcrumbs[] = $categoryName;

        return implode(' > ', array_slice($breadcrumbs, -2, 2));
    }
}
