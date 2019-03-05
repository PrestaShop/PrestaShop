<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @param $dbPrefix
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
