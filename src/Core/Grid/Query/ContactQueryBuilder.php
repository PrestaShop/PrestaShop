<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ContactsQueryBuilder is responsible for building queries for Contacts grid data.
 */
final class ContactQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var array
     */
    private $contextShopsIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $languageId
     * @param array $contextShopsIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $languageId,
        array $contextShopsIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->languageId = $languageId;
        $this->contextShopsIds = $contextShopsIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('c.id_contact, c.email, cl.name, cl.description, c.customer_service')
            ->groupBy('c.id_contact');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT c.id_contact)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $allowedFilters = [
            'id_contact',
            'name',
            'email',
            'description',
            'customer_service',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'contact', 'c')
            ->innerJoin('c', $this->dbPrefix . 'contact_lang', 'cl', 'c.id_contact = cl.id_contact')
            ->innerJoin('c', $this->dbPrefix . 'contact_shop', 'cs', 'c.id_contact = cs.id_contact')
            ->andWhere('cl.`id_lang`= :language')
            ->andWhere('cs.`id_shop` IN (:shops)')
            ->setParameter('language', $this->languageId)
            ->setParameter('shops', $this->contextShopsIds, Connection::PARAM_INT_ARRAY);

        foreach ($filters as $name => $value) {
            if (!in_array($name, $allowedFilters, true)) {
                continue;
            }

            if ('id_contact' === $name) {
                $qb->andWhere('c.`id_contact` = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, '%' . $value . '%');
        }

        return $qb;
    }
}
