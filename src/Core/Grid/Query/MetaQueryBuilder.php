<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class MetaQueryBuilder is responsible for providing data for seo & urls list.
 */
final class MetaQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextIdLang;

    /**
     * @var int
     */
    private $contextIdShop;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * MetaQueryBuilder constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextIdLang
     * @param int $contextIdShop
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextIdLang,
        $contextIdShop
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextIdLang = $contextIdLang;
        $this->contextIdShop = $contextIdShop;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('m.`id_meta`, m.`page`, l.`title`, l.`url_rewrite`');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(m.`id_meta`)');

        return $qb;
    }

    /**
     * Gets query builder with common sql for meta table.
     *
     * @param array $filters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $availableFilters = [
            'id_meta',
            'page',
            'title',
            'url_rewrite',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'meta', 'm')
            ->innerJoin(
                'm',
                $this->dbPrefix . 'meta_lang',
                'l',
                'm.`id_meta` = l.`id_meta`'
            );

        $qb->andWhere('l.`id_lang` = :id_lang');
        $qb->andWhere('l.`id_shop` = :id_shop');

        $qb->setParameters([
            'id_lang' => $this->contextIdLang,
            'id_shop' => $this->contextIdShop,
        ]);

        $qb->andWhere('m.`configurable`=1');

        foreach ($filters as $name => $value) {
            if (!in_array($name, $availableFilters, true)) {
                continue;
            }

            if ('id_meta' === $name) {
                $qb->andWhere('m.`id_meta` = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            if ('page' === $name) {
                $qb->andWhere('m.`page` LIKE :' . $name);
                $qb->setParameter($name, '%' . $value . '%');

                continue;
            }

            $qb->andWhere('l.`' . $name . '` LIKE :' . $name);
            $qb->setParameter($name, '%' . $value . '%');
        }

        return $qb;
    }
}
