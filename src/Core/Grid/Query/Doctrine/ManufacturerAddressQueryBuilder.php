<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Doctrine;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ManufacturerAddressQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var string
     */
    private $langId;

    /**
     * @param Connection $connection
     * @param string $tablePrefix
     * @param string $langId
     */
    public function __construct(Connection $connection, $tablePrefix)
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
        $this->langId = \Context::getContext()->language->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('a.id_address, m.name, a.firstname, a.lastname, a.postcode, a.city, cl.name as country_name')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit())
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('COUNT(*)');

        return $qb;
    }

    private function getQueryBuilderByFilters(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->tablePrefix.'address', 'a')
            ->leftJoin('a', $this->tablePrefix.'country_lang', 'cl', 'cl.id_country = a.id_country AND cl.id_lang = :lang')
                ->setParameter('lang', $this->langId)
            ->leftJoin('a', $this->tablePrefix.'manufacturer', 'm', 'm.id_manufacturer = a.id_manufacturer')
        ;

        foreach ($filters as $name => $value) {
            if (in_array($name, ['id_address'])) {
                $qb->andWhere("$name = :$name");
                $qb->setParameter($name, $value);

                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, '%'.$value.'%');
        }

        $qb->andWhere('a.id_customer = 0')
            ->andWhere('a.id_supplier = 0')
            ->andWhere('a.id_warehouse = 0')
            ->andWhere('a.deleted = 0');

        return $qb;
    }
}
