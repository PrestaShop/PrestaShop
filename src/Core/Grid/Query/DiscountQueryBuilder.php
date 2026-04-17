<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
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
        private readonly ConfigurationInterface $configuration,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $quantityUsedSubquery = sprintf(
            '(SELECT COUNT(*)
                FROM %sorders o
                INNER JOIN %sorder_cart_rule ocr ON ocr.id_order = o.id_order
                WHERE ocr.deleted = 0
                AND ocr.id_cart_rule = cr.id_cart_rule
                AND o.current_state != %d
            )',
            $this->dbPrefix,
            $this->dbPrefix,
            (int) $this->configuration->get('PS_OS_ERROR')
        );

        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select(
                'cr.id_cart_rule AS id_discount,
                crl.name,
                crt.discount_type,
                crtl.name AS discount_type_name,
                cr.code,
                ' . $quantityUsedSubquery . ' AS quantity_used,
                cr.total_quantity,
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
            ->leftJoin(
                'crt',
                $this->dbPrefix . 'cart_rule_type_lang',
                'crtl',
                'crt.id_cart_rule_type = crtl.id_cart_rule_type AND crtl.id_lang = :contextLangId'
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
            'discount_type' => 'crt.id_cart_rule_type',
            'active' => 'cr.active',
        ];

        $exactMatchFilters = ['id_discount', 'discount_type', 'active'];

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
        if (!is_string($value) || empty($value)) {
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
