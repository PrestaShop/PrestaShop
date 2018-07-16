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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use RuntimeException;
use Shop;

class ManufacturerRepository implements DoctrineQueryBuilderInterface
{
    use NormalizeFieldTrait;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param $tablePrefix
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        $tablePrefix
    )
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;

        $context = $contextAdapter->getContext();

        if (!$context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $context->shop;
        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->shopId = $shop->getContextualShopId();
    }

    /**
     * @return mixed
     */
    public function getManufacturers()
    {
        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            m.id_manufacturer AS manufacturer_id,
            m.name
            FROM {table_prefix}manufacturer m
            INNER JOIN {table_prefix}manufacturer_shop ms ON (
                ms.id_shop = :shop_id AND
                ms.id_manufacturer = m.id_manufacturer
            )'
        );

        $statement = $this->connection->prepare($query);

        $statement->bindValue('shop_id', $this->shopId);

        $statement->execute();

        $rows = $statement->fetchAll();

        return $this->castNumericToInt($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->createQueryBuilderFromSearchCriteria($searchCriteria);
        $qb->select('m.id_manufacturer, m.name, m.active, COUNT(p.id_product) as products_count')
            ->addSelect('(
                SELECT COUNT(a.id_manufacturer) as addresses_count
                FROM '.$this->tablePrefix.'address a
                WHERE a.id_manufacturer = m.id_manufacturer
                    AND a.deleted = 0
                GROUP BY a.id_manufacturer    
            ) as addresses_count')
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
        $qb = $this->createQueryBuilderFromSearchCriteria($searchCriteria);
        $qb->select('COUNT(m.id_manufacturer)');

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function createQueryBuilderFromSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->tablePrefix.'manufacturer', 'm')
            ->leftJoin('m', $this->tablePrefix.'product', 'p', 'm.id_manufacturer = p.id_manufacturer')
            ->groupBy('m.id_manufacturer');

        foreach ($searchCriteria->getFilters() as $name => $value) {
            $value = trim($value);
            if ('' === $value) {
                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, '%'.$value.'%');
        }

        return $qb;
    }
}
