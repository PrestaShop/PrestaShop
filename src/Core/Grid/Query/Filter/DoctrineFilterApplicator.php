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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Exception\InvalidFilterDataException;

final class DoctrineFilterApplicator implements DoctrineFilterApplicatorInterface
{
    private const CASE_BOTH_FIELDS_EXIST = 1;
    private const CASE_ONLY_MIN_FIELD_EXISTS = 2;
    private const CASE_ONLY_MAX_FIELD_EXISTS = 3;

    /**
     * {@inheritdoc}
     */
    public function apply(QueryBuilder $qb, SqlFilters $filters, array $filterValues)
    {
        if (empty($filterValues)) {
            return;
        }

        foreach ($filters->getFilters() as $filter) {
            $sqlField = $filter['sql_field'];
            $filterName = $filter['filter_name'];

            if (!isset($filterValues[$filterName])) {
                continue;
            }

            $value = $filterValues[$filterName];

            switch ($filter['comparison']) {
                case SqlFilters::WHERE_STRICT:
                    $qb->andWhere("$sqlField = :$filterName");
                    $qb->setParameter($filterName, $value);

                    break;
                case SqlFilters::WHERE_LIKE:
                    $qb->andWhere("$sqlField LIKE :$filterName");
                    $qb->setParameter($filterName, '%' . $value . '%');

                    break;
                case SqlFilters::HAVING_LIKE:
                    $qb->andHaving("$sqlField LIKE :$filterName");
                    $qb->setParameter($filterName, '%' . $value . '%');

                    break;
                case SqlFilters::WHERE_DATE:
                    if (isset($value['from'])) {
                        $name = sprintf('%s_from', $filterName);

                        $qb->andWhere("$sqlField >= :$name");
                        $qb->setParameter($name, sprintf('%s %s', $value['from'], '0:0:0'));
                    }

                    if (isset($value['to'])) {
                        $name = sprintf('%s_to', $filterName);

                        $qb->andWhere("$sqlField <= :$name");
                        $qb->setParameter($name, sprintf('%s %s', $value['to'], '23:59:59'));
                    }

                    break;
                case SqlFilters::MIN_MAX:
                    $minFieldSqlCondition = sprintf('%s >= :%s_min', $sqlField, $filterName);
                    $maxFieldSqlCondition = sprintf('%s <= :%s_max', $sqlField, $filterName);

                    switch ($this->computeMinMaxCase($value)) {
                        case self::CASE_BOTH_FIELDS_EXIST:
                            $qb->andWhere(sprintf('%s AND %s', $minFieldSqlCondition, $maxFieldSqlCondition));
                            $qb->setParameter(sprintf('%s_min', $filterName), $value['min_field']);
                            $qb->setParameter(sprintf('%s_max', $filterName), $value['max_field']);
                            break;
                        case self::CASE_ONLY_MIN_FIELD_EXISTS:
                            $qb->andWhere($minFieldSqlCondition);
                            $qb->setParameter(sprintf('%s_min', $filterName), $value['min_field']);
                            break;
                        case self::CASE_ONLY_MAX_FIELD_EXISTS:
                            $qb->andWhere($maxFieldSqlCondition);
                            $qb->setParameter(sprintf('%s_max', $filterName), $value['max_field']);
                            break;
                    }
                    break;
            }
        }
    }

    /**
     * @param array<string, int> $value
     *
     * @return int
     */
    private function computeMinMaxCase(array $value): int
    {
        $minFieldExists = isset($value['min_field']);
        $maxFieldExists = isset($value['max_field']);

        if ($minFieldExists && $maxFieldExists) {
            return self::CASE_BOTH_FIELDS_EXIST;
        }
        if ($minFieldExists) {
            return self::CASE_ONLY_MIN_FIELD_EXISTS;
        }

        if ($maxFieldExists) {
            return self::CASE_ONLY_MAX_FIELD_EXISTS;
        }

        throw new InvalidFilterDataException('Min max filter wasn\'t applied correctly');
    }
}
