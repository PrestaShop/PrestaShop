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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Category\Repository;

use Category;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Provides access to Category data source
 */
class CategoryRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param CategoryId $categoryId
     *
     * @return Category
     *
     * @throws CategoryNotFoundException
     */
    public function get(CategoryId $categoryId): Category
    {
        try {
            /** @var Category $category */
            $category = $this->getObjectModel(
                $categoryId->getValue(),
                Category::class,
                CategoryException::class
            );
        } catch (CategoryException $e) {
            throw new CategoryNotFoundException($categoryId, $e->getMessage());
        }

        return $category;
    }

    /**
     * @todo: multishop not considered
     *
     * @param CategoryId[] $categoryIds
     *
     * @return array<int, array<int, string>> [$categoryId => [$langId => $categoryName]]
     */
    public function getLocalizedNames(array $categoryIds): array
    {
        $categoryIds = array_map(function ($categoryId) {
            return $categoryId->getValue();
        }, $categoryIds
        );

        $qb = $this->connection->createQueryBuilder();
        $qb->select('cl.name, cl.id_category, cl.id_lang')
            ->from($this->dbPrefix . 'category_lang', 'cl')
            ->where($qb->expr()->in('id_category', ':categoryIds'))
            ->setParameter('categoryIds', $categoryIds, Connection::PARAM_INT_ARRAY)
        ;

        $results = $qb->execute()->fetchAllAssociative();

        if (!$results) {
            return [];
        }

        $localizedNamesByIds = [];
        foreach ($results as $result) {
            $categoryId = (int) $result['id_category'];
            $langId = (int) $result['id_lang'];

            $localizedNamesByIds[$categoryId][$langId] = $result['name'];
        }

        return $localizedNamesByIds;
    }

    /**
     * @param CategoryId $categoryId
     *
     * @throws CategoryNotFoundException
     * @throws CoreException
     */
    public function assertCategoryExists(CategoryId $categoryId): void
    {
        try {
            $this->assertObjectModelExists(
                $categoryId->getValue(),
                'category',
                CategoryException::class
            );
        } catch (CategoryException $e) {
            throw new CategoryNotFoundException($categoryId, $e->getMessage());
        }
    }

    /**
     * Provides ids of categories which are not unique per shop and language.
     *
     * @param ShopId $shopId
     * @param LanguageId $languageId
     *
     * @return CategoryId[]
     */
    public function getDuplicateNameIds(ShopId $shopId, LanguageId $languageId): array
    {
        $duplicateNames = $this->getDuplicateNames($shopId, $languageId);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('c.id_category')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'category_lang',
                'cl',
                'c.id_category = cl.id_category'
            )
            ->where('id_lang = :langId')
            ->andWhere('id_shop = :shopId')
            ->andWhere('c.level_depth >= 1')
            ->andWhere($qb->expr()->in('cl.name', ':duplicateNames'))
            ->setParameter('langId', $languageId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('duplicateNames', $duplicateNames, Connection::PARAM_STR_ARRAY)
        ;

        $results = $qb->execute()->fetchAllAssociative();

        $categoryIds = [];
        foreach ($results as $result) {
            $categoryIds[] = new CategoryId((int) $result['id_category']);
        }

        return $categoryIds;
    }

    /**
     * @param CategoryId $categoryId
     * @param LanguageId $languageId
     *
     * @return string[]
     */
    public function getBreadcrumbParts(CategoryId $categoryId, LanguageId $languageId): array
    {
        $categoryQb = $this->connection->createQueryBuilder();
        $categoryQb
            ->select('cl.name, c.nleft, c.nright')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin('c', $this->dbPrefix . 'category_lang', 'cl', 'c.id_category = cl.id_category')
            ->andWhere('c.id_category = :categoryId')
            ->andWhere('cl.id_lang = :languageId')
            ->setParameter('categoryId', $categoryId->getValue())
            ->setParameter('languageId', $languageId->getValue())
        ;

        $category = $categoryQb->execute()->fetchAssociative();

        if (empty($category)) {
            throw new CategoryNotFoundException($categoryId, 'Cannot find breadcrumb because category does not exist');
        }

        $categoryName = $category['name'];

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('c.id_category, cl.name')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin('c', $this->dbPrefix . 'category_lang', 'cl', 'c.id_category = cl.id_category')
            ->andWhere('c.nleft < :left AND c.nright > :right')
            ->andWhere('cl.id_lang = :languageId')
            ->andWhere('c.level_depth >= 1')
            ->addGroupBy('c.id_category')
            ->addOrderBy('c.id_category', 'ASC')
            ->addOrderBy('c.position', 'ASC')
            ->setParameter('left', (int) $category['nleft'])
            ->setParameter('right', (int) $category['nright'])
            ->setParameter('languageId', $languageId->getValue())
        ;

        $results = $qb->execute()->fetchAllAssociative();

        if ($results) {
            $parentNames = array_column($results, 'name');
        }

        $parentNames[] = $categoryName;

        return $parentNames;
    }

    /**
     * @param CategoryId $categoryId
     * @param LanguageId $languageId
     * @param string $separator
     *
     * @return string
     */
    public function getBreadcrumb(CategoryId $categoryId, LanguageId $languageId, string $separator = ' > '): string
    {
        return implode($separator, $this->getBreadcrumbParts($categoryId, $languageId));
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return CategoryId[]
     */
    public function getProductCategoryIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('cp.id_category')
            ->from($this->dbPrefix . 'category_product', 'cp')
            ->where('cp.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        if ($shopConstraint->getShopId()) {
            $qb
                ->innerJoin(
                    'cp',
                    $this->dbPrefix . 'category_shop',
                    'cs',
                    'cp.id_category = cs.id_category'
                )
                ->andWhere('cs.id_shop = :shopId')
                ->setParameter('shopId', $shopConstraint->getShopId()->getValue())
            ;
        }

        $results = $qb->execute()->fetchAllAssociative();

        $categoryIds = [];
        foreach ($results as $result) {
            $categoryIds[] = new CategoryId((int) $result['id_category']);
        }

        return $categoryIds;
    }

    /**
     * @param ProductId $productId
     * @param CategoryId[] $addedCategories
     */
    public function addProductAssociations(ProductId $productId, array $addedCategories): void
    {
        // Get current categories for all shops, only the one completely absent should be added to avoid duplicate entry
        $currentCategoryIds = $this->getProductCategoryIds($productId, ShopConstraint::allShops());
        $newCategories = array_filter($addedCategories, static function (CategoryId $addedCategoryId) use ($currentCategoryIds): bool {
            foreach ($currentCategoryIds as $currentCategory) {
                if ($currentCategory->getValue() === $addedCategoryId->getValue()) {
                    return false;
                }
            }

            return true;
        });

        if (empty($newCategories)) {
            return;
        }

        $categoryIds = array_unique(array_map(static function (CategoryId $categoryId): int {
            return $categoryId->getValue();
        }, $newCategories));

        $maxPositions = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'category_product', 'cp')
            ->select('cp.id_category, MAX(cp.position) AS position')
            ->andWhere('cp.id_category IN (:categories)')
            ->setParameter('categories', $categoryIds, Connection::PARAM_INT_ARRAY)
            ->groupBy('cp.id_category')
            ->execute()->fetchAllAssociative()
        ;

        // Prepare new rows for each category if the max position was not found it's the first product associated
        // to the category so its position is 1, else we increment
        foreach ($categoryIds as $categoryId) {
            $maxCategoryPosition = 1;
            foreach ($maxPositions as $maxPosition) {
                if ((int) $maxPosition['id_category'] === $categoryId) {
                    $maxCategoryPosition = (int) $maxPosition['position'] + 1;
                }
            }

            $this->connection->insert(
                $this->dbPrefix . 'category_product',
                [
                    'id_category' => $categoryId,
                    'id_product' => $productId->getValue(),
                    'position' => $maxCategoryPosition,
                ]
            );
        }
    }

    /**
     * @param ProductId $productId
     * @param CategoryId[] $removedCategories
     */
    public function removeProductAssociations(ProductId $productId, array $removedCategories): void
    {
        $categoryIds = array_values(array_unique(array_map(static function (CategoryId $categoryId): int {
            return $categoryId->getValue();
        }, $removedCategories)));

        $currentPositions = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'category_product', 'cp')
            ->select('cp.id_category, cp.position')
            ->where('cp.id_product = :productId')
            ->andWhere('cp.id_category IN (:categories)')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('categories', $categoryIds, Connection::PARAM_INT_ARRAY)
            ->execute()->fetchAllAssociative()
        ;

        $this->connection
            ->createQueryBuilder()
            ->delete($this->dbPrefix . 'category_product')
            ->where('id_product = :productId')
            ->andWhere('id_category IN (:categories)')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('categories', $categoryIds, Connection::PARAM_INT_ARRAY)
            ->execute()
        ;

        // Decrement positions for each category impacted
        foreach ($currentPositions as $currentPosition) {
            $this->connection
                ->createQueryBuilder()
                ->update($this->dbPrefix . 'category_position', 'cp')
                ->set('position', 'position - 1')
                ->where('cp.id_category = :categoryId AND cp.position > :position')
                ->setParameters([
                    'categoryId' => (int) $currentPosition['id_category'],
                    'position' => (int) $currentPosition['position'],
                ])
            ;
        }
    }

    public function getShopDefaultCategory(ShopId $shopId): CategoryId
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'shop', 's')
            ->select('s.id_category')
            ->where('s.id_shop = :shopId')
            ->setParameter('shopId', $shopId->getValue())
            ->execute()
            ->fetchAssociative()
        ;

        if (empty($result['id_category'])) {
            throw new ShopNotFoundException(sprintf('Could not find shop with id %d', $shopId->getValue()));
        }

        return new CategoryId((int) $result['id_category']);
    }

    /**
     * Returns defined product default category for the specified shop, but only if it is associated.
     *
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return CategoryId|null
     */
    public function getProductDefaultCategory(ProductId $productId, ShopId $shopId): ?CategoryId
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product_shop', 'ps')
            ->innerJoin(
                'ps',
                $this->dbPrefix . 'category_product',
                'cp',
                'cp.id_product = ps.id_product AND cp.id_category = ps.id_category_default'
            )
            ->innerJoin(
                'cp',
                $this->dbPrefix . 'category_shop',
                'cs',
                'cs.id_category = cp.id_category AND cs.id_shop = :shopId'
            )
            ->select('ps.id_category_default')
            ->where('ps.id_product = :productId')
            ->andWhere('ps.id_shop = :shopId')
            ->setParameters([
                'productId' => $productId->getValue(),
                'shopId' => $shopId->getValue(),
            ])
            ->execute()
            ->fetchAssociative()
        ;

        return !empty($result['id_category_default']) ? new CategoryId((int) $result['id_category_default']) : null;
    }

    /**
     * Provides category names which are not unique per shop and language.
     *
     * e.g. if certain shop contains following categories in english language:
     *      Clothes -> Men, Bags -> Men, Clothes -> Woman, Bags -> Women,
     *      then method should return ["Men", "Women"]
     *
     * @param ShopId $shopId
     * @param LanguageId $languageId
     *
     * @return string[]
     */
    protected function getDuplicateNames(ShopId $shopId, LanguageId $languageId): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('cl.name')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'category_lang', 'cl',
                'c.id_category = cl.id_category'
            )
            ->where('id_lang = :langId')
            ->andWhere('id_shop = :shopId')
            ->andWhere('c.level_depth >= 1')
            ->having('COUNT(cl.name) > 1')
            ->setParameter('langId', $languageId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->groupBy('cl.name')
        ;

        $results = $qb->execute()->fetchAllAssociative();

        $names = [];
        foreach ($results as $result) {
            $names[] = $result['name'];
        }

        return $names;
    }
}
