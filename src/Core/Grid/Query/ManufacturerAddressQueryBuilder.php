<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ManufacturerAddressQueryBuilder is responsible for building queries for manufacturers addresses grid data.
 */
final class ManufacturerAddressQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param LanguageContext $languageContext
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private LanguageContext $languageContext
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('a.id_address, m.name, a.firstname, a.lastname, a.postcode, a.city, cl.name as country');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT a.`id_address`)');

        return $qb;
    }

    /**
     * Gets query builder with common sql needed for manufacturer addresses grid.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilderByFilters(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'address', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'country_lang',
                'cl',
                'cl.id_country = a.id_country AND cl.id_lang = :lang'
            )
            ->setParameter('lang', $this->languageContext->getId())
            ->leftJoin(
                'a',
                $this->dbPrefix . 'manufacturer',
                'm', 'm.id_manufacturer = a.id_manufacturer'
            )
            ->andWhere('a.id_customer = 0')
            ->andWhere('a.id_supplier = 0')
            ->andWhere('a.id_warehouse = 0')
            ->andWhere('a.deleted = 0')
            ->andWhere('a.id_manufacturer > 0')
        ;
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_address' => 'a.id_address',
            'name' => 'm.name',
            'firstname' => 'a.firstname',
            'lastname' => 'a.lastname',
            'postcode' => 'a.postcode',
            'city' => 'a.city',
            'country' => 'a.id_country',
        ];
        $exactMatchingFilters = ['id_address', 'country'];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if (in_array($filterName, $exactMatchingFilters, true)) {
                if (empty($value)) {
                    continue;
                }

                $qb->andWhere($allowedFiltersMap[$filterName] . " = :$filterName")
                    ->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . " LIKE :$filterName")
                ->setParameter($filterName, '%' . $value . '%');
        }
    }
}
