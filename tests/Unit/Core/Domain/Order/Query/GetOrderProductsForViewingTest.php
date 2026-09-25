<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\Query;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;

class GetOrderProductsForViewingTest extends TestCase
{
    public function testConstructorMatchesPaginatedFactory(): void
    {
        $direct = new GetOrderProductsForViewing(1, 10, 20, QuerySorting::DESC);
        $factory = GetOrderProductsForViewing::paginated(1, 10, 20, QuerySorting::DESC);

        $this->assertEquals($factory->getOrderId()->getValue(), $direct->getOrderId()->getValue());
        $this->assertSame($factory->getOffset(), $direct->getOffset());
        $this->assertSame($factory->getLimit(), $direct->getLimit());
        $this->assertEquals($factory->getProductsSorting()->getValue(), $direct->getProductsSorting()->getValue());
    }

    public function testConstructorMatchesAllFactory(): void
    {
        $direct = new GetOrderProductsForViewing(1, null, null, QuerySorting::ASC);
        $factory = GetOrderProductsForViewing::all(1, QuerySorting::ASC);

        $this->assertEquals($factory->getOrderId()->getValue(), $direct->getOrderId()->getValue());
        $this->assertNull($direct->getOffset());
        $this->assertNull($direct->getLimit());
        $this->assertSame($factory->getOffset(), $direct->getOffset());
        $this->assertSame($factory->getLimit(), $direct->getLimit());
        $this->assertEquals($factory->getProductsSorting()->getValue(), $direct->getProductsSorting()->getValue());
    }

    public function testConstructorDefaultsToAscSorting(): void
    {
        $instance = new GetOrderProductsForViewing(1);

        $this->assertEquals(1, $instance->getOrderId()->getValue());
        $this->assertNull($instance->getOffset());
        $this->assertNull($instance->getLimit());
        $this->assertEquals(QuerySorting::ASC, $instance->getProductsSorting()->getValue());
    }
}
