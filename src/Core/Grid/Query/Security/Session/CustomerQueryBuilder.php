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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Security\Session;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerQueryBuilder is responsible for building queries for profiles grid data.
 */
class CustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('cs.id_customer_session, c.id_customer, c.firstname, c.lastname, c.email, cs.date_upd')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(cs.id_customer_session)')
        ;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'customer_session', 'cs')
            ->join('cs', $this->dbPrefix . 'customer', 'c', 'c.id_customer = cs.id_customer')
        ;

        $allowedFilters = [
            'date_upd',
            'email',
            'firstname',
            'id_customer',
            'id_customer_session',
            'lastname',
        ];

        foreach ($filters as $name => $value) {
            if (!in_array($name, $allowedFilters, true)) {
                continue;
            }

            if ('id_customer_session' === $name) {
                $qb->andWhere('cs.id_customer_session = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            if ('id_customer' === $name) {
                $qb->andWhere('c.id_customer = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            if ('date_upd' === $name) {
                if (isset($value['from'])) {
                    $qb->andWhere('cs.date_upd >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $value['from']));
                }

                if (isset($value['to'])) {
                    $qb->andWhere('cs.date_upd <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $value['to']));
                }

                continue;
            }

            $qb->andWhere(
                sprintf(
                    'c.%s LIKE :%s',
                    $name,
                    $name
                )
            );

            $qb->setParameter($name, '%' . $value . '%');
        }

        return $qb;
    }
}
