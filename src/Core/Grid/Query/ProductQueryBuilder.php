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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Defines all required sql statements to render products list.
 */
final class ProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var bool
     */
    private $isStockSharingBetweenShopGroupEnabled;

    /**
     * @var int
     */
    private $contextShopGroupId;

    /**
     * @var DoctrineFilterApplicatorInterface
     */
    private $filterApplicator;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        int $contextShopId,
        int $contextShopGroupId,
        bool $isStockSharingBetweenShopGroupEnabled,
        DoctrineFilterApplicatorInterface $filterApplicator,
        Configuration $configuration
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopId = $contextShopId;
        $this->isStockSharingBetweenShopGroupEnabled = $isStockSharingBetweenShopGroupEnabled;
        $this->contextShopGroupId = $contextShopGroupId;
        $this->filterApplicator = $filterApplicator;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->addSelect('p.`id_product`, p.`reference`')
            ->addSelect('ps.`price` AS `price_tax_excluded`, ps.`ecotax` AS `ecotax_tax_excluded`, ps.`id_tax_rules_group`, ps.`active`')
            ->addSelect('pl.`name`, pl.`link_rewrite`')
            ->addSelect('cl.`name` AS `category`')
            ->addSelect('img_shop.`id_image`')
            ->addSelect('img_lang.legend')
            ->addSelect('p.`id_tax_rules_group`')
        ;

        // When ecotax is enabled the real final price is the sum of price and ecotax so we fetch an extra alias column that is used for sorting
        if ($this->configuration->getBoolean('PS_USE_ECOTAX')) {
            $qb->addSelect('(ps.`price` + ps.`ecotax`) AS `final_price_tax_excluded`');
        } else {
            $qb->addSelect('(ps.`price` + ps.`ecotax`) AS `final_price_tax_excluded`');
        }

        if ($this->configuration->getBoolean('PS_STOCK_MANAGEMENT')) {
            $qb->addSelect('sa.`quantity`');
        }

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
        ;

        // Any sorting that is not based on position can be applied
        if ($searchCriteria->getOrderBy() !== 'position') {
            $this->searchCriteriaApplicator->applySorting($searchCriteria, $qb);
        } elseif (array_key_exists('id_category', $searchCriteria->getFilters())) {
            // Sort by position only works when we filter by category, so we need to be cautious and apply it only when the filter is present
            $this->searchCriteriaApplicator->applySorting($searchCriteria, $qb);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(p.`id_product`)');

        return $qb;
    }

    /**
     * Gets query builder.
     *
     * @param array $filterValues
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filterValues): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.`id_product` = p.`id_product` AND ps.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :id_lang AND pl.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'ps',
                $this->dbPrefix . 'category_lang',
                'cl',
                'cl.`id_category` = ps.`id_category_default` AND cl.`id_lang` = :id_lang AND cl.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'ps',
                $this->dbPrefix . 'image_shop',
                'img_shop',
                'img_shop.`id_product` = ps.`id_product` AND img_shop.`cover` = 1 AND img_shop.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'img_shop',
                $this->dbPrefix . 'image_lang',
                'img_lang',
                'img_shop.`id_image` = img_lang.`id_image` AND img_lang.`id_lang` = :id_lang'
            )
            ->andWhere('p.`state`=1')
        ;

        $filteredCategoryId = $this->getFilteredCategoryId($filterValues);
        if (null !== $filteredCategoryId) {
            $qb
                ->rightJoin(
                    'p',
                    $this->dbPrefix . 'category_product',
                    'pc',
                    'p.`id_product` = pc.`id_product` AND pc.id_category = :categoryId'
                )
                ->setParameter('categoryId', $filteredCategoryId)
                ->addSelect('pc.`position`, pc.`id_category`')
            ;
        }

        $isStockManagementEnabled = $this->configuration->getBoolean('PS_STOCK_MANAGEMENT');

        if ($isStockManagementEnabled) {
            $stockOnCondition =
                'sa.`id_product` = p.`id_product`
                    AND sa.`id_product_attribute` = 0
                ';

            if ($this->isStockSharingBetweenShopGroupEnabled) {
                $stockOnCondition .= '
                     AND sa.`id_shop` = 0 AND sa.`id_shop_group` = :id_shop_group
                ';
            } else {
                $stockOnCondition .= '
                     AND sa.`id_shop` = :id_shop AND sa.`id_shop_group` = 0
                ';
            }

            $qb->leftJoin(
                'p',
                $this->dbPrefix . 'stock_available',
                'sa',
                $stockOnCondition
            );

            $qb->setParameter('id_shop_group', $this->contextShopGroupId);
        }

        $sqlFilters = new SqlFilters();
        $sqlFilters
            ->addFilter(
                'id_product',
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        // When ecotax is enabled the real final price is the sum of price and ecotax so the filters must be setup accordingly
        if ($this->configuration->getBoolean('PS_USE_ECOTAX')) {
            $sqlFilters->addFilter(
                'final_price_tax_excluded',
                '(ps.`price` + ps.`ecotax`)',
                SqlFilters::MIN_MAX
            );
        } else {
            $sqlFilters->addFilter(
                'final_price_tax_excluded',
                'ps.`price`',
                SqlFilters::MIN_MAX
            );
        }

        if ($isStockManagementEnabled) {
            $sqlFilters
                ->addFilter(
                    'quantity',
                    'sa.`quantity`',
                    SqlFilters::MIN_MAX
                )
            ;
        }

        $this->filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $qb->setParameter('id_shop', $this->contextShopId);
        $qb->setParameter('id_lang', $this->contextLanguageId);

        foreach ($filterValues as $filterName => $filter) {
            if ('active' === $filterName) {
                $qb->andWhere('ps.`active` = :active');
                $qb->setParameter('active', $filter);
            }

            if ('name' === $filterName) {
                $qb->andWhere('pl.`name` LIKE :name');
                $qb->setParameter('name', '%' . $filter . '%');
            }

            if ('reference' === $filterName) {
                $qb->andWhere('p.`reference` LIKE :reference');
                $qb->setParameter('reference', '%' . $filter . '%');
            }

            if ('category' === $filterName) {
                $qb->andWhere('cl.`name` LIKE :category');
                $qb->setParameter('category', '%' . $filter . '%');
            }

            // Filter by position is only relevant when a category has been selected
            if (array_key_exists('id_category', $filterValues) && 'position' === $filterName) {
                $qb->andWhere('pc.`position` = :position');
                $qb->setParameter('position', $filter);
            }
        }

        return $qb;
    }

    private function getFilteredCategoryId(array $filterValues): ?int
    {
        foreach ($filterValues as $filterName => $filter) {
            if ('id_category' === $filterName) {
                return (int) $filter;
            }
        }

        return null;
    }
}
