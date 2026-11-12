<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Lists the products associated with one entity (a file, a category...) through an association table with an
 * `id_product` column. The name and status of each product are the ones of the context shop, or of the product's
 * default shop when the context is not a single shop.
 */
abstract class AbstractAssociatedProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        protected readonly int $associatedEntityId,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('p.`id_product`, p.`reference`, pl.`name`, ps.`active`')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'p', 'id_product')
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(p.`id_product`)')
        ;
    }

    /**
     * The association table, without the prefix
     */
    abstract protected function getAssociationTable(): string;

    /**
     * The column of the association table holding the entity ID
     */
    abstract protected function getAssociatedEntityColumn(): string;

    /**
     * @param array<string, mixed> $filters
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $shopConstraint = $this->shopContext->getShopConstraint();
        $productShop = null !== $shopConstraint->getShopId() ? ':shopId' : 'p.`id_shop_default`';

        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . $this->getAssociationTable(), 'assoc')
            ->innerJoin('assoc', $this->dbPrefix . 'product', 'p', 'p.`id_product` = assoc.`id_product`')
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.`id_product` = p.`id_product` AND ps.`id_shop` = ' . $productShop
            )
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :languageId AND pl.`id_shop` = ' . $productShop
            )
            ->where('assoc.`' . $this->getAssociatedEntityColumn() . '` = :associatedEntityId')
            ->setParameter('associatedEntityId', $this->associatedEntityId)
            ->setParameter('languageId', $this->languageContext->getId())
        ;

        if (null !== $shopConstraint->getShopId()) {
            $qb->setParameter('shopId', $shopConstraint->getShopId()->getValue());
        }

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $likeFilters = [
            'reference' => 'p.`reference`',
            'name' => 'pl.`name`',
        ];

        foreach ($filters as $filterName => $value) {
            if ('' === (string) $value) {
                continue;
            }

            if ('id_product' === $filterName) {
                $qb->andWhere('p.`id_product` = :id_product')
                    ->setParameter('id_product', (int) $value);

                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere('ps.`active` = :active')
                    ->setParameter('active', (int) (bool) $value);

                continue;
            }

            if (isset($likeFilters[$filterName])) {
                $qb->andWhere($likeFilters[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . addcslashes((string) $value, '%_\\') . '%');
            }
        }
    }
}
