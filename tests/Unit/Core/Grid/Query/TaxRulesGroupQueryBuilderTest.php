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

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\TaxRulesGroupQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRulesGroupFilters;

class TaxRulesGroupQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    private function getMockConnection(): Connection
    {
        $mock = $this->createMock(Connection::class);

        $mock->method('createQueryBuilder')->willReturn(
            $this->getMockQueryBuilder()
        );

        return $mock;
    }

    private function getMockQueryBuilder(): QueryBuilder
    {
        return $this->createPartialMock(QueryBuilder::class, []);
    }

    private function getFilters(): TaxRulesGroupFilters
    {
        return new TaxRulesGroupFilters(TaxRulesGroupFilters::getDefaults());
    }

    private function getMockDoctrineSearchCriteriaApplicatorInterface(): DoctrineSearchCriteriaApplicatorInterface
    {
        $mock = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);

        $mock->method('applyPagination')->willReturnSelf();
        $mock->method('applySorting')->willReturnSelf();

        return $mock;
    }

    /**
     * @dataProvider dataProviderQueryBuilder
     *
     * @param Filters $filters
     * @param array $qbQueryParts
     * @param array $qbQueryPartsCount
     * @param array $qbParameters
     *
     * @return void
     */
    public function testQueryBuild(Filters $filters, array $qbQueryParts, array $qbQueryPartsCount, array $qbParameters): void
    {
        $queryBuilder = new TaxRulesGroupQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockDoctrineSearchCriteriaApplicatorInterface(),
            []
        );

        $qb = $queryBuilder->getSearchQueryBuilder($filters);

        $this->assertEquals(
            $qbQueryParts,
            $qb->getQueryParts()
        );

        $this->assertEquals(
            $qbParameters,
            $qb->getParameters()
        );
    }

    /**
     * @dataProvider dataProviderQueryBuilder
     *
     * @param Filters $filters
     * @param array $qbQueryParts
     * @param array $qbQueryPartsCount
     * @param array $qbParameters
     *
     * @return void
     */
    public function testCountQueryBuild(Filters $filters, array $qbQueryParts, array $qbQueryPartsCount, array $qbParameters): void
    {
        $queryBuilder = new TaxRulesGroupQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockDoctrineSearchCriteriaApplicatorInterface(),
            []
        );

        $qb = $queryBuilder->getCountQueryBuilder($filters);

        $this->assertEquals(
            $qbQueryPartsCount,
            $qb->getQueryParts()
        );

        $this->assertEquals(
            $qbParameters,
            $qb->getParameters()
        );
    }

    public function dataProviderQueryBuilder(): iterable
    {
        // Default
        $defaultFilters = $this->getFilters();
        $defaultQueryParts = [
            'select' => [
                'trg.`id_tax_rules_group`, trg.`name`, trg.`active`',
            ],
            'distinct' => false,
            'from' => [
                [
                    'table' => self::DB_PREFIX . 'tax_rules_group',
                    'alias' => 'trg',
                ],
            ],
            'join' => [
                'trg' => [
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'tax_rules_group_shop',
                        'joinAlias' => 'trgs',
                        'joinCondition' => 'trg.`id_tax_rules_group` = trgs.`id_tax_rules_group`',
                    ],
                ],
            ],
            'set' => [],
            'where' => new CompositeExpression(
                'AND',
                [
                    'trgs.`id_shop` IN (:contextShopIds)',
                    'trg.`deleted` = 0',
                ]
            ),
            'groupBy' => [],
            'having' => null,
            'orderBy' => [],
            'values' => [],
        ];
        $defaultParameters = [
            'contextShopIds' => [],
        ];

        yield [
            $defaultFilters,
            $defaultQueryParts,
            array_merge($defaultQueryParts, ['select' => [0 => 'COUNT(DISTINCT trg.`id_tax_rules_group`)']]),
            $defaultParameters,
        ];

        // With Data
        $filters1 = clone $defaultFilters;
        $filters1->addFilter([
            'name' => 'data',
        ]);
        $queryParts1 = $defaultQueryParts;
        $queryParts1['where'] = new CompositeExpression(
            'AND',
            [
                'trgs.`id_shop` IN (:contextShopIds)',
                'trg.`deleted` = 0',
                'trg.name LIKE :name',
            ]
        );
        $parameters1 = $defaultParameters;
        $parameters1['name'] = '%data%';

        yield [
            $filters1,
            $queryParts1,
            array_merge($queryParts1, ['select' => [0 => 'COUNT(DISTINCT trg.`id_tax_rules_group`)']]),
            $parameters1,
        ];

        // With Percent Data
        $filters2 = clone $defaultFilters;
        $filters2->addFilter([
            'name' => 'da%ta',
        ]);
        $queryParts2 = $defaultQueryParts;
        $queryParts2['where'] = new CompositeExpression(
            'AND',
            [
                'trgs.`id_shop` IN (:contextShopIds)',
                'trg.`deleted` = 0',
                'trg.name LIKE :name',
            ]
        );
        $parameters2 = $defaultParameters;
        $parameters2['name'] = '%da\%ta%';

        yield [
            $filters2,
            $queryParts2,
            array_merge($queryParts2, ['select' => [0 => 'COUNT(DISTINCT trg.`id_tax_rules_group`)']]),
            $parameters2,
        ];
    }
}
