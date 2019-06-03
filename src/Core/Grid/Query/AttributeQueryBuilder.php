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
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class AttributeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(Connection $connection, $dbPrefix)
    {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * Get query that searches grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        //@todo:
        $qb = $this->getQueryBuilder();
        $qb->select('a.id_attribute, al.name AS value, a.position');

        return $qb;
    }

    /**
     * Get query that counts grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('COUNT(DISTINCT a.`id_attribute`)')
        ;

        return $qb;
    }

    private function getQueryBuilder()
    {
        $qb = $this->connection->createQueryBuilder()
        ->from($this->dbPrefix . 'attribute', 'a');

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_group',
            'ag',
            'a.id_attribute_group = ag.id_attribute_group'
        );
        $qb->andWhere('ag.id_attribute_group = 1');

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_lang',
            'al',
            'a.id_attribute = al.id_attribute AND al.id_lang = 1'
        );

        return $qb;
    }
}
