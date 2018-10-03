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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Builder;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class LanguageQueryBuilder provides query builders for languages grid.
 */
final class LanguageQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->getLanguageQueryBuilder($searchCriteria)
            ->select('p.*')
        ;

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getLanguageQueryBuilder($searchCriteria)->select('COUNT(id_lang)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getLanguageQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'lang', 'l')
        ;

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria)
    {
        $allowedFilters = [
            'id_lang',
            'name',
            'iso_code',
            'language_code',
            'date_format_lite',
            'date_format_full',
            'active',
        ];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_lang', 'active'])) {
                $builder->andWhere($filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);

                continue;
            }

            $builder->andWhere($filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
