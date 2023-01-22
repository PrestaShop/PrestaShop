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

namespace PrestaShop\PrestaShop\Adapter\Category\Repository;

use Category;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
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
     * @param ShopId $shopId
     *
     * @return CategoryId[]
     */
    public function getProductCategoryIds(ProductId $productId, ShopId $shopId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('cp.id_category')
            ->from($this->dbPrefix . 'category_product', 'cp')
            ->innerJoin(
                'cp',
                $this->dbPrefix . 'category_shop',
                'cs',
                'cp.id_category = cs.id_category'
            )
            ->where('cp.id_product = :productId')
            ->andWhere('cs.id_shop = :shopId')
            ->setParameters([
                'productId' => $productId->getValue(),
                'shopId' => $shopId->getValue(),
            ])
        ;

        $results = $qb->execute()->fetchAllAssociative();

        $categoryIds = [];
        foreach ($results as $result) {
            $categoryIds[] = new CategoryId((int) $result['id_category']);
        }

        return $categoryIds;
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
