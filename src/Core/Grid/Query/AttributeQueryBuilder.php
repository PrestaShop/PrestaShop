<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @var int
     */
    private $contextShopId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $attributeGroupId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param $contextShopId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId,
        $attributeGroupId,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        $contextShopId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->attributeGroupId = $attributeGroupId;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->contextShopId = $contextShopId;
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
            ->select('a.id_attribute, a.color, a.id_attribute_group, al.name AS value, a.position');

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
            ->setParameter('contextShopId', $this->contextShopId);

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $qb->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_shop',
                'shop',
                'a.id_attribute = shop.id_attribute AND shop.id_shop = :contextShopId'
            );
            $qb->andWhere('shop.id_shop = :contextShopId');
        }

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_group',
            'ag',
            'a.id_attribute_group = ag.id_attribute_group')
            ->andWhere('ag.id_attribute_group = :attributeGroupId');

        $qb->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_lang',
            'al',
            'a.id_attribute = al.id_attribute AND al.id_lang = :contextLangId'
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
            $qb->andWhere('a.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }
    }
}
