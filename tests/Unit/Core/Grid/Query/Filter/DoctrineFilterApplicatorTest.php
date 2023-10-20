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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;

class DoctrineFilterApplicatorTest extends TestCase
{
    /**
     * @var DoctrineFilterApplicator
     */
    private $doctrineFilterApplicator;

    public function setUp(): void
    {
        $this->doctrineFilterApplicator = new DoctrineFilterApplicator();
    }

    /** Tests min max price filter when both min and max are used */
    public function testMinMaxFilterBoth(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );

        $filterValues = [
            'price_tax_excluded' => [
                'min_field' => '10',
                'max_field' => '20',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` >= :price_tax_excluded_min AND ps.`price` <= :price_tax_excluded_max', (string) $wherePart);
    }

    /** Tests min max price filter when only min is present */
    public function testMinMaxPriceFilterMin(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );
        $filterValues = [
            'price_tax_excluded' => [
                'min_field' => '10',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` >= :price_tax_excluded_min', (string) $wherePart);
    }

    /** Tests min max price filter when only max is used */
    public function testMinMaxPriceFilterMax(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );

        $filterValues = [
            'price_tax_excluded' => [
                'max_field' => '10',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` <= :price_tax_excluded_max', (string) $wherePart);
    }
}
