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

/**
 * Attachment query builder builds search & count data for attachment grid.
 */
final class AttachmentQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var string
     */
    private $employeeIdLang;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param string $employeeIdLang
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        string $employeeIdLang
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('a.`id_attachment`, al.`name`, a.`file`, a.`file_size`')
            ->addSelect('COALESCE(virtual_product_attachment.`product_count`, 0) AS products')
        ;

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
            ->select('COUNT(DISTINCT a.`id_attachment`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql used for displaying webservice list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'attachment', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'attachment_lang',
                'al',
                'a.`id_attachment` = al.`id_attachment`'
            );

        $productCountQb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product_attachment', 'pa')
            ->select('pa.`id_attachment`, COUNT(*) as product_count')
            ->groupBy('id_attachment');

        $qb->leftJoin('a',
            '(' . $productCountQb->getSQL() . ')',
            'virtual_product_attachment',
            'a.`id_attachment` = virtual_product_attachment.`id_attachment`');

        $qb->andWhere('al.`id_lang` = :employee_id_lang');
        $qb->setParameter('employee_id_lang', $this->employeeIdLang);
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to attachments query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_attachment' => 'a.id_attachment',
            'name' => 'al.name',
            'file_size' => 'a.file_size',
            'products' => 'virtual_product_attachment.product_count',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if ('id_attachment' === $filterName) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            if ('products' === $filterName && $value === '0') {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' IS NULL');

                $qb->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                ->setParameter($filterName, '%' . $value . '%');
        }
    }
}
