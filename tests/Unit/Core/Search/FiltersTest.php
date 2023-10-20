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

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Tests\Resources\SampleFilters;

class FiltersTest extends TestCase
{
    public function testFiltersBuildDefaults(): void
    {
        $filters = Filters::buildDefaults();
        $this->assertEquals(0, $filters->getOffset());
        $this->assertEquals(null, $filters->getOrderBy());
        $this->assertEquals(null, $filters->getOrderWay());
        $this->assertEquals(10, $filters->getLimit());
        $this->assertEquals([], $filters->getFilters());
    }

    public function testSampleFiltersBuildDefaults(): void
    {
        $filters = SampleFilters::buildDefaults();
        $this->assertEquals(42, $filters->getOffset());
        $this->assertEquals('id_sample', $filters->getOrderBy());
        $this->assertEquals('desc', $filters->getOrderWay());
        $this->assertEquals(51, $filters->getLimit());
        $this->assertEquals([], $filters->getFilters());
    }

    /**
     * @dataProvider getValidOrderBy
     */
    public function testValidOrderBy(string $validOrderBy): void
    {
        $filters = new SampleFilters(['orderBy' => $validOrderBy]);
        $this->assertEquals($validOrderBy, $filters->getOrderBy());

        $filters = new Filters(['orderBy' => $validOrderBy]);
        $this->assertEquals($validOrderBy, $filters->getOrderBy());
    }

    public function getValidOrderBy(): iterable
    {
        yield ['test'];
        yield ['test_underscore'];
        yield ['test-hyphen'];
        yield ['test-69'];
        yield ['test-amazing!'];
        yield ['ca.test'];
        yield ['`ca`.test'];
        yield ['ca.`test`'];
        yield ['`ca`.`test`'];
    }

    /**
     * @dataProvider getInvalidOrderBy
     */
    public function testInvalidOrderByd(string $invalidOrderBy): void
    {
        $filters = new Filters(['orderBy' => $invalidOrderBy]);
        $this->assertNull($filters->getOrderBy());
    }

    public function getInvalidOrderBy(): iterable
    {
        // Special characters are not accepted
        yield ['test?'];
        yield ['test$'];
        yield ['test€'];
        yield ['test%'];
        yield ['test)'];
        yield ['test('];
        yield ['test '];
        yield [' test'];

        // Incorrect dots
        yield ['.ca.test'];
        yield ['ca.test.'];

        // Opening back-quotes without closing (or vice versa)
        yield ['`ca.test'];
        yield ['ca`.test'];
        yield ['ca.`test'];
        yield ['ca.test`'];
        yield ['`ca.test`'];
        yield ['ca`.test`'];

        // Back-quotes must wrap alias or column not open in the middle
        yield ['test`test`'];
        yield ['test`test`test'];
        yield ['test`test`.test'];
        yield ['test`test`test.test'];

        yield ['ca.test`test`.'];
    }

    /**
     * @dataProvider getValidOrderWay
     */
    public function testSampleFiltersWithValidOrderWay(string $orderWay): void
    {
        $filters = new SampleFilters(['sortOrder' => $orderWay]);
        $this->assertEquals($orderWay, $filters->getOrderWay());

        $filters = new Filters(['sortOrder' => $orderWay]);
        $this->assertEquals($orderWay, $filters->getOrderWay());
    }

    public function getValidOrderWay(): iterable
    {
        yield ['ASC'];
        yield ['DESC'];
        yield ['asc'];
        yield ['desc'];
        yield ['dEsC'];
        yield ['AsC'];
    }

    /**
     * @dataProvider getInvalidOrderWay
     */
    public function testSampleFiltersWithInvalidOrderWay(string $orderWay): void
    {
        $filters = new Filters(['sortOrder' => $orderWay]);
        $this->assertNull($filters->getOrderWay());
    }

    public function getInvalidOrderWay(): iterable
    {
        yield ['test'];
        yield ['RAND()'];
    }
}
