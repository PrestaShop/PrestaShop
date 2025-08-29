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
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Category\NameBuilder\CategoryDisplayNameBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler\GetCategoriesTreeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see GetCategoriesTree using legacy object model
 */
#[AsQueryHandler]
final class GetCategoriesTreeHandler implements GetCategoriesTreeHandlerInterface
{
    /**
     * @var CategoryDisplayNameBuilder
     */
    private $displayNameBuilder;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var int
     */
    private $rootCategoryId;

    /**
     * @param CategoryDisplayNameBuilder $displayNameBuilder
     * @param ContextStateManager $contextStateManager
     * @param ShopRepository $shopRepository
     * @param int $rootCategoryId
     */
    public function __construct(
        CategoryDisplayNameBuilder $displayNameBuilder,
        ContextStateManager $contextStateManager,
        ShopRepository $shopRepository,
        int $rootCategoryId
    ) {
        $this->displayNameBuilder = $displayNameBuilder;
        $this->contextStateManager = $contextStateManager;
        $this->shopRepository = $shopRepository;
        $this->rootCategoryId = $rootCategoryId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCategoriesTree $query): array
    {
        $langId = $query->getLanguageId();
        $this->contextStateManager
            ->saveCurrentContext()
            ->setShop($this->shopRepository->get($query->getShopId()))
        ;

        try {
            $nestedCategories = Category::getNestedCategories($this->rootCategoryId, $langId->getValue(), false);
            $nestedCategories = $nestedCategories[$this->rootCategoryId]['children'] ?? [];
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        return $this->buildCategoriesTree($nestedCategories, $query->getShopId(), $langId);
    }

    /**
     * @param array<string, array<string, mixed>> $categories
     * @param ShopId $shopId
     * @param LanguageId $langId
     *
     * @return CategoryForTree[]
     */
    private function buildCategoriesTree(array $categories, ShopId $shopId, LanguageId $langId): array
    {
        $categoriesTree = [];
        foreach ($categories as $category) {
            $categoryId = (int) $category['id_category'];

            $categoryName = $category['name'];
            $categoryActive = (bool) $category['active'];
            $categoryChildren = [];

            if (!empty($category['children'])) {
                $categoryChildren = $this->buildCategoriesTree(
                    $category['children'],
                    $shopId,
                    $langId
                );
            }

            $displayName = $this->displayNameBuilder->build(
                $categoryName,
                $shopId,
                $langId,
                new CategoryId($categoryId)
            );

            $categoriesTree[] = new CategoryForTree(
                $categoryId,
                $categoryActive,
                $categoryName,
                $displayName,
                $categoryChildren
            );
        }

        return $categoriesTree;
    }
}
