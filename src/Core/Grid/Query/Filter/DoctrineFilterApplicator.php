<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
