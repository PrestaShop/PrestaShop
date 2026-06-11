<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\TaxRuleQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\TaxRuleFilters;

class TaxRuleQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';
    private const LANGUAGE_ID = 1;

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

    private function getFilters(): TaxRuleFilters
    {
        return new TaxRuleFilters(TaxRuleFilters::getDefaults());
    }

    private function getMockDoctrineSearchCriteriaApplicatorInterface(): DoctrineSearchCriteriaApplicatorInterface
    {
        $mock = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);

        $mock->method('applyPagination')->willReturnSelf();
        $mock->method('applySorting')->willReturnSelf();

        return $mock;
    }

    private function getMockLanguageContext(): LanguageContext
    {
        $mock = $this->createMock(LanguageContext::class);

        $mock->method('getId')->willReturn(self::LANGUAGE_ID);

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
        $queryBuilder = new TaxRuleQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockDoctrineSearchCriteriaApplicatorInterface(),
            $this->getMockLanguageContext()
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
        $queryBuilder = new TaxRuleQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockDoctrineSearchCriteriaApplicatorInterface(),
            $this->getMockLanguageContext()
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
        $select = [
            'tr.`id_tax_rule`',
            'tr.`id_tax_rules_group`',
            'tr.`description`',
            'tr.`id_country` AS country_id',
            'cl.`name` AS country_name',
            'tr.`id_state` AS state_id',
            'IFNULL(s.`name`, \'--\') AS state_name',
            'CASE '
                . ' WHEN CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`) = \'0 - 0\''
                . ' THEN \'--\' ELSE CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`)'
            . ' END AS zipcode',
            'tr.behavior',
            't.rate',
            'txl.`name` AS tax_name',
        ];
        $countSelect = ['COUNT(DISTINCT tr.`id_tax_rule`)'];

        // Default: no filter at all, the query lists tax rules across all groups
        $defaultFilters = $this->getFilters();
        $defaultQueryParts = [
            'select' => $select,
            'distinct' => false,
            'from' => [
                [
                    'table' => self::DB_PREFIX . 'tax_rule',
                    'alias' => 'tr',
                ],
            ],
            'join' => [
                'tr' => [
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'country',
                        'joinAlias' => 'c',
                        'joinCondition' => 'tr.`id_country` = c.`id_country`',
                    ],
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'country_lang',
                        'joinAlias' => 'cl',
                        'joinCondition' => 'tr.`id_country` = cl.`id_country` AND cl.`id_lang` = :idLang ',
                    ],
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'state',
                        'joinAlias' => 's',
                        'joinCondition' => 'tr.`id_country` = s.`id_country` AND tr.`id_state` = s.`id_state`',
                    ],
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'tax',
                        'joinAlias' => 't',
                        'joinCondition' => 'tr.`id_tax` = t.`id_tax`',
                    ],
                ],
                't' => [
                    [
                        'joinType' => 'left',
                        'joinTable' => self::DB_PREFIX . 'tax_lang',
                        'joinAlias' => 'txl',
                        'joinCondition' => 't.`id_tax` = txl.`id_tax` AND txl.`id_lang` = :idLang',
                    ],
                ],
            ],
            'set' => [],
            'where' => null,
            'groupBy' => [],
            'having' => null,
            'orderBy' => [],
            'values' => [],
            'for_update' => null,
        ];
        $defaultParameters = [
            'idLang' => self::LANGUAGE_ID,
        ];

        yield 'no filter' => [
            $defaultFilters,
            $defaultQueryParts,
            array_merge($defaultQueryParts, ['select' => $countSelect]),
            $defaultParameters,
        ];

        // Scoped to a tax rules group
        $groupFilters = clone $defaultFilters;
        $groupFilters->addFilter([
            'taxRulesGroupId' => 2,
        ]);
        $groupQueryParts = $defaultQueryParts;
        $groupQueryParts['where'] = 'tr.`id_tax_rules_group` = :idTaxRulesGroup';
        $groupParameters = $defaultParameters;
        $groupParameters['idTaxRulesGroup'] = 2;

        yield 'tax rules group filter' => [
            $groupFilters,
            $groupQueryParts,
            array_merge($groupQueryParts, ['select' => $countSelect]),
            $groupParameters,
        ];

        // Scoped to a tax rules group using the column alias key (used by the Admin API)
        $groupAliasFilters = clone $defaultFilters;
        $groupAliasFilters->addFilter([
            'id_tax_rules_group' => 2,
        ]);

        yield 'tax rules group filter with column alias key' => [
            $groupAliasFilters,
            $groupQueryParts,
            array_merge($groupQueryParts, ['select' => $countSelect]),
            $groupParameters,
        ];

        // Column alias filters (used by the Admin API): country_name, tax_name and behavior
        $aliasFilters = clone $defaultFilters;
        $aliasFilters->addFilter([
            'taxRulesGroupId' => 2,
            'country_name' => 'Fra',
            'tax_name' => 'VAT',
            'behavior' => 1,
        ]);
        $aliasQueryParts = $defaultQueryParts;
        $aliasQueryParts['where'] = new CompositeExpression(
            'AND',
            [
                'tr.`id_tax_rules_group` = :idTaxRulesGroup',
                'cl.`name` LIKE :country',
                'txl.`name` LIKE :taxName',
                'tr.`behavior` = :behavior',
            ]
        );
        $aliasParameters = $defaultParameters;
        $aliasParameters['idTaxRulesGroup'] = 2;
        $aliasParameters['country'] = '%Fra%';
        $aliasParameters['taxName'] = '%VAT%';
        $aliasParameters['behavior'] = 1;

        yield 'column alias filters' => [
            $aliasFilters,
            $aliasQueryParts,
            array_merge($aliasQueryParts, ['select' => $countSelect]),
            $aliasParameters,
        ];

        // Country id filter (used by the Admin API): filters on the raw id_country column
        $countryIdFilters = clone $defaultFilters;
        $countryIdFilters->addFilter([
            'taxRulesGroupId' => 2,
            'country_id' => 8,
        ]);
        $countryIdQueryParts = $defaultQueryParts;
        $countryIdQueryParts['where'] = new CompositeExpression(
            'AND',
            [
                'tr.`id_tax_rules_group` = :idTaxRulesGroup',
                'tr.`id_country` = :countryId',
            ]
        );
        $countryIdParameters = $defaultParameters;
        $countryIdParameters['idTaxRulesGroup'] = 2;
        $countryIdParameters['countryId'] = 8;

        yield 'country id filter' => [
            $countryIdFilters,
            $countryIdQueryParts,
            array_merge($countryIdQueryParts, ['select' => $countSelect]),
            $countryIdParameters,
        ];

        // Legacy filter keys used by the back office grid still work
        $legacyFilters = clone $defaultFilters;
        $legacyFilters->addFilter([
            'taxRulesGroupId' => 2,
            'country' => 'Fra',
            'state' => 'Cal',
        ]);
        $legacyQueryParts = $defaultQueryParts;
        $legacyQueryParts['where'] = new CompositeExpression(
            'AND',
            [
                'tr.`id_tax_rules_group` = :idTaxRulesGroup',
                'cl.`name` LIKE :country',
                's.`name` LIKE :state',
            ]
        );
        $legacyParameters = $defaultParameters;
        $legacyParameters['idTaxRulesGroup'] = 2;
        $legacyParameters['country'] = '%Fra%';
        $legacyParameters['state'] = '%Cal%';

        yield 'legacy back office filter keys' => [
            $legacyFilters,
            $legacyQueryParts,
            array_merge($legacyQueryParts, ['select' => $countSelect]),
            $legacyParameters,
        ];
    }
}
