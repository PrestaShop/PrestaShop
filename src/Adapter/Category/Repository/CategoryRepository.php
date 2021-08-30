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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

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
        /** @var Category $category */
        $category = $this->getObjectModel(
            $categoryId->getValue(),
            Category::class,
            CategoryNotFoundException::class
        );

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
     * @param CategoryId $categoryId
     * @param LanguageId $languageId
     * @param string $separator
     *
     * @return string
     */
    public function getBreadcrumb(CategoryId $categoryId, LanguageId $languageId, string $separator = ' > '): string
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('cl.name, c.nleft, c.nright')
            ->from($this->dbPrefix . 'category', 'c')
            ->innerJoin('c', $this->dbPrefix . 'category_lang', 'cl', 'c.id_category = cl.id_category')
            ->andWhere('c.id_category = :categoryId')
            ->andWhere('cl.id_lang = :languageId')
            ->setParameter('categoryId', $categoryId->getValue())
            ->setParameter('languageId', $languageId->getValue())
        ;

        $result = $qb->execute()->fetchAll();
        if (empty($result)) {
            throw new CategoryNotFoundException($categoryId, 'Cannot find breadcrumb because category does not exist');
        }

        $category = $result[0];
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

        $result = $qb->execute()->fetchAll();
        $parentNames = [];
        foreach ($result as $category) {
            $parentNames[] = $category['name'];
        }
        $parentNames[] = $categoryName;

        return implode($separator, $parentNames);
    }
}
