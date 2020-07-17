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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Provides sql for attributes group > attribute list
 */
final class AttributeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $attributeGroupId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId,
        $attributeGroupId,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->attributeGroupId = $attributeGroupId;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * Get query that searches grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('DISTINCT a.id_attribute, a.color, a.id_attribute_group, al.name AS value, a.position');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * Get query that counts grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT a.`id_attribute`)');

        return $qb;
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'attribute', 'a')
            ->setParameter('contextLangId', $this->contextLangId)
            ->setParameter('attributeGroupId', $this->attributeGroupId)
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->andWhere('al.id_lang = :contextLangId')
            ->andWhere('ag.id_attribute_group = :attributeGroupId');

        if (!$this->multistoreContextChecker->isAllShopContext()) {
            $qb->andWhere('ashop.id_shop IN (:contextShopIds)');
        }

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_shop',
            'ashop',
            'a.id_attribute = ashop.id_attribute'
        );

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_group',
            'ag',
            'a.id_attribute_group = ag.id_attribute_group');

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_lang',
            'al',
            'a.id_attribute = al.id_attribute'
        );

        $this->applyFilters($filters, $qb);

        return $qb;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFiltersMap = [
            'id_attribute' => 'a.id_attribute',
            'value' => 'al.name',
            'position' => 'a.position',
            'color' => 'a.color',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if ('value' === $filterName || 'color' === $filterName) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }

            if ('position' === $filterName) {
                // When filtering by position,
                // value must be decreased by 1,
                // since position value in database starts at 0,
                // but for user display positions are increased by 1.
                if (is_numeric($value)) {
                    --$value;
                } else {
                    $value = null;
                }
            }

            $qb->andWhere('a.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }
    }
}
