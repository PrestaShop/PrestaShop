<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
