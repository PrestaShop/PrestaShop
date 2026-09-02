<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
