<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;

/**
 * This query builder is used to get the details of a specific product in each of its associated shops.
 */
class ProductShopsQueryBuilder extends ProductQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        if (!$searchCriteria instanceof ShopSearchCriteriaInterface) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria, expected a %s', ShopSearchCriteriaInterface::class));
        }

        $qb = parent::getSearchQueryBuilder($searchCriteria);
        $qb
            ->addSelect('ps.id_shop')
        ;

        // In case only group shops are request we add a condition to filter only shops from group
        if ($searchCriteria->getShopConstraint()->getShopGroupId()) {
            $qb
                ->innerJoin(
                    'ps',
                    $this->dbPrefix . 'shop',
                    'gs',
                    'gs.id_shop = ps.id_shop AND gs.id_shop_group = :shopGroupId'
                )
                ->setParameter('shopGroupId', $searchCriteria->getShopConstraint()->getShopGroupId()->getValue())
            ;
        }

        return $qb;
    }

    /**
     * We perform no filtering on the shops since the purpose is to get all the details.
     *
     * @param string $sql
     * @param string $tableAlias
     * @param int|null $shopId
     * @param int|null $filteredShopGroupId
     *
     * @return string
     */
    protected function addShopCondition(string $sql, string $tableAlias, ?int $shopId, ?int $filteredShopGroupId): string
    {
        return $sql . ' AND ' . $tableAlias . '.`id_shop` = ps.`id_shop`';
    }

    /**
     * We perform no filtering on the stock, but we need to handle the stock shared by group.
     *
     * @param int|null $sharedStockGroupId
     * @param int|null $shopId
     * @param int|null $filteredShopGroupId
     *
     * @return string
     */
    protected function getStockOnCondition(?int $sharedStockGroupId, ?int $shopId, ?int $filteredShopGroupId): string
    {
        return 'sa.`id_product` = p.`id_product` AND sa.`id_product_attribute` = 0 AND (sa.`id_shop` = ps.`id_shop` OR sa.`id_shop_group` = s.`id_shop_group`)';
    }
}
