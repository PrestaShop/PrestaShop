<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Filter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterInterface;

class FilterCollectionTest extends TestCase
{
    public function testFilterCollectionIsEmptyByDefault()
    {
        $filters = new FilterCollection();

        $this->assertEmpty($filters->all());

        return $filters;
    }

    /**
     * @depends testFilterCollectionIsEmptyByDefault
     */
    public function testFilterCanBeAddedToCollection(FilterCollection $filters)
    {
        $filters->add($this->getFilterMock('first'));
        $filters->add($this->getFilterMock('second'));
        $filters->add($this->getFilterMock('third'));

        $this->assertCount(3, $filters->all());

        return $filters;
    }

    /**
     * @depends testFilterCanBeAddedToCollection
     */
    public function testFilterCanBeRemovedFromCollection(FilterCollection $filters)
    {
        $filters->remove('second');

        $this->assertCount(2, $filters->all());
    }

    private function getFilterMock($name)
    {
        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getName')
            ->willReturn($name);

        return $filter;
    }
}
