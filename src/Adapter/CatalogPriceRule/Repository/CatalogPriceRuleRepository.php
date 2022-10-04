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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class CatalogPriceRuleRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    public function getByProductId(
        ProductId $productId,
        LanguageId $langId,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $qb = $this->getCatalogPriceRulesQueryBuilder($langId)
            ->select('spr.id_specific_price_rule, spr.name as specific_price_rule_name, currency_lang.symbol, country_lang.name as lang_name, shop.name as shop_name, group_lang.name as group_name, spr.from_quantity, spr.reduction_type, spr.reduction, spr.from, spr.to')
        ;
        $qb = $this->addCatalogPriceRuleCondition($qb, $productId);
        $qb->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $langId
     *
     * @return int
     */
    public function countByProductId(ProductId $productId, LanguageId $langId): int
    {
        $qb = $this->getCatalogPriceRulesQueryBuilder($langId)
        ;

        $qb = $this->addCatalogPriceRuleCondition($qb, $productId);
        /** I can't select only count because I need meets_condition for having clause,
         * thus all price rules needs to be caunted afterwards
         */
        return count($qb->execute()->fetchAll());
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    public function getAll(
        LanguageId $langId,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $qb = $this->getCatalogPriceRulesQueryBuilder($langId)
            ->select('spr.id_specific_price_rule, spr.name as specific_price_rule_name, currency_lang.symbol, country_lang.name as lang_name, shop.name as shop_name, group_lang.name as group_name, spr.from_quantity, spr.reduction_type, spr.reduction, spr.from, spr.to')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param LanguageId $langId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function countCatalogPriceRules(LanguageId $langId): int
    {
        $qb = $this->getCatalogPriceRulesQueryBuilder($langId)
            ->select('COUNT(spr.id_specific_price_rule) AS total_catalog_price_rules')
        ;

        return (int) $qb->execute()->fetch()['total_catalog_price_rules'];
    }

    /**
     * @param QueryBuilder $qb
     * @param ProductId $productId
     *
     * @return QueryBuilder
     */
    private function addCatalogPriceRuleCondition(QueryBuilder $qb, ProductId $productId): QueryBuilder
    {
        /**
         * This selects checks whether or not conditions for product ID are met. Valid results are either 1(meets at least one of condition groups) or null meaning no conditions
         * Sum in subselect checks if number of conditions met for each group is equal to number of conditions in group. If it's not at least one of conditions in the group is not met.
         * The outer Sum checks if at least one of the condition groups is met.
         */
        $qb->addSelect('
                SUM(
                    (SELECT
                        SUM(
                            CASE
                                WHEN sprc.type  = \'manufacturer\' AND p.id_manufacturer = sprc.value THEN 1
                                WHEN sprc.type  = \'supplier\' AND p.id_supplier = sprc.value THEN 1
                                WHEN sprc.type  = \'attribute\' AND (SELECT id_product_attribute FROM ps_product_attribute pa WHERE pa.id_product = p.id_product AND pa.id_product_attribute = sprc.value) THEN 1
                                WHEN sprc.type  = \'feature\' AND (SELECT id_feature FROM ps_feature_product fp WHERE fp.id_product = p.id_product AND fp.id_feature_value = sprc.value) THEN 1
                                WHEN sprc.type  = \'category\' AND (SELECT id_category FROM ps_category_product cp WHERE cp.id_product = p.id_product AND cp.id_category = sprc.value) THEN 1
                                ELSE 0
                            END
                    ) = COUNT(*)
                    FROM ps_specific_price_rule_condition_group AS sprcg
                    LEFT JOIN ps_specific_price_rule_condition sprc
                        ON sprcg.id_specific_price_rule_condition_group = sprc.id_specific_price_rule_condition_group
                    LEFT JOIN ps_product p
                        ON id_product = :productId
                    WHERE sprcg.id_specific_price_rule_condition_group = sprcgouter.id_specific_price_rule_condition_group
                    )
                ) > 0 as meets_condition'
        )
            ->leftJoin('spr',
                $this->dbPrefix . 'specific_price_rule_condition_group',
                'sprcgouter',
                'spr.id_specific_price_rule = sprcgouter.id_specific_price_rule'
            )
            ->setParameter('productId', $productId->getValue())
            ->groupBy('spr.id_specific_price_rule')
            ->having('(meets_condition = 1 OR meets_condition IS NULL)');

        return $qb;
    }

    private function getCatalogPriceRulesQueryBuilder(LanguageId $langId): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'specific_price_rule', 'spr')
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'currency_lang',
                'currency_lang',
                'spr.id_currency = currency_lang.id_currency AND currency_lang.id_lang = :langId'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'shop',
                'shop',
                'spr.id_shop = shop.id_shop'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'country_lang',
                'country_lang',
                'spr.id_country = country_lang.id_country AND country_lang.id_lang = :langId'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'group_lang',
                'group_lang',
                'spr.id_group = group_lang.id_group AND group_lang.id_lang = :langId'
            )
            ->orderBy('spr.id_specific_price_rule', 'asc')
            ->setParameter('langId', $langId->getValue());
    }
}
