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

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for discount list
 */
class DiscountQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly LanguageContext $languageContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select(
                'cr.id_cart_rule AS id_discount,
                crl.name,
                crt.type,
                cr.code,
                cr.date_from,
                cr.date_to,
                cr.active'
            );
        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT cr.`id_cart_rule`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql for discounts listing.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cart_rule', 'cr')
        ;

        $qb
            ->leftJoin(
                'cr',
                $this->dbPrefix . 'cart_rule_lang',
                'crl',
                'cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = :contextLangId'
            )
            ->leftJoin(
                'cr',
                $this->dbPrefix . 'cart_rule_type',
                'crt',
                'cr.id_cart_rule_type = crt.id_cart_rule_type'
            )
            ->setParameter('contextLangId', $this->languageContext->getId())
        ;

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFiltersAliasMap = [
            'id_discount' => 'cr.id_cart_rule',
            'name' => 'crl.name',
            'code' => 'cr.code',
            'type' => 'crt.type',
            'active' => 'cr.active',
        ];

        $exactMatchFilters = ['id_discount', 'type', 'active'];

        $now = new DateTimeImmutable();

        foreach ($filters as $filterName => $value) {
            if ($filterName === 'period_filter') {
                $this->applyPeriodFilter($qb, $value, $now);

                continue;
            }

            if (!array_key_exists($filterName, $allowedFiltersAliasMap)
                && $filterName !== 'date_from_filter'
                && $filterName !== 'date_to_filter') {
                continue;
            }

            if ($filterName === 'date_from_filter' && is_array($value)) {
                if (!empty($value['from'])) {
                    $qb->andWhere('cr.date_from >= :dateFromStart')
                        ->setParameter('dateFromStart', $value['from']);
                }
                if (!empty($value['to'])) {
                    $qb->andWhere('cr.date_from <= :dateFromEnd')
                        ->setParameter('dateFromEnd', $value['to']);
                }
                continue;
            }

            if ($filterName === 'date_to_filter' && is_array($value)) {
                if (!empty($value['from'])) {
                    $qb->andWhere('cr.date_to >= :dateToStart')
                        ->setParameter('dateToStart', $value['from']);
                }
                if (!empty($value['to'])) {
                    $qb->andWhere('cr.date_to <= :dateToEnd')
                        ->setParameter('dateToEnd', $value['to']);
                }
                continue;
            }

            if (in_array($filterName, $exactMatchFilters, true)) {
                $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $value);
                continue;
            }

            $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, "%$value%");
        }
    }

    private function applyPeriodFilter(QueryBuilder $qb, mixed $value, DateTimeImmutable $now): void
    {
        if (!is_string($value) || $value === '' || $value === DiscountSettings::PERIOD_FILTER_ALL) {
            return;
        }

        $formattedNow = $now->format('Y-m-d H:i:s');

        switch ($value) {
            case DiscountSettings::PERIOD_FILTER_ACTIVE:
                $qb->andWhere('cr.date_from <= :period_now_active');
                $qb->andWhere('(
                    cr.date_to >= :period_now_active
                    OR cr.date_to IS NULL
                    OR UNIX_TIMESTAMP(cr.date_to) IS NULL
                )');
                $qb->setParameter('period_now_active', $formattedNow);

                break;

            case DiscountSettings::PERIOD_FILTER_SCHEDULED:
                $qb->andWhere('cr.date_from > :period_now_scheduled');
                $qb->setParameter('period_now_scheduled', $formattedNow);

                break;

            case DiscountSettings::PERIOD_FILTER_EXPIRED:
                $qb->andWhere('cr.date_to < :period_now_expired');
                $qb->andWhere('cr.date_to IS NOT NULL');
                $qb->andWhere('UNIX_TIMESTAMP(cr.date_to) IS NOT NULL');
                $qb->setParameter('period_now_expired', $formattedNow);

                break;
        }
    }
}
