<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\Specification;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Specification\LazyNumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

class LazyNumberCollectionTest extends TestCase
{
    /**
     * @var string[]
     */
    private $built = [];

    private function createCollection(): LazyNumberCollection
    {
        $this->built = [];

        return new LazyNumberCollection(['EUR', 'USD', 'GBP'], function (string $code): NumberInterface {
            $this->built[] = $code;

            return $this->createMock(NumberInterface::class);
        });
    }

    public function testItemsAreBuiltOnFirstGetOnly(): void
    {
        $collection = $this->createCollection();

        $this->assertSame([], $this->built);
        $usd = $collection->get('USD');
        $this->assertSame($usd, $collection->get('USD'));
        $this->assertSame(['USD'], $this->built);
    }

    public function testUnknownIndexIsNotBuilt(): void
    {
        $collection = $this->createCollection();

        $this->assertNull($collection->get('XXX'));
        $this->assertSame([], $this->built);
    }

    public function testAllCountAndIteratorBuildEverythingInOrder(): void
    {
        $collection = $this->createCollection();
        $collection->get('GBP');

        $this->assertSame(['EUR', 'USD', 'GBP'], array_keys($collection->all()));
        $this->assertCount(3, $collection);
        $this->assertSame(['EUR', 'USD', 'GBP'], array_keys(iterator_to_array($collection)));
        $this->assertSame(['GBP', 'EUR', 'USD'], $this->built);
    }

    public function testAddRemoveAndClear(): void
    {
        $collection = $this->createCollection();
        $spec = $this->createMock(NumberInterface::class);

        $collection->add('USD', $spec);
        $this->assertSame($spec, $collection->get('USD'));
        $this->assertSame([], $this->built);

        $collection->remove(['EUR', 'USD']);
        $this->assertNull($collection->get('EUR'));
        $this->assertNull($collection->get('USD'));
        $this->assertCount(1, $collection);

        $collection->clear();
        $this->assertSame([], $collection->all());
        $this->assertNull($collection->get('GBP'));
    }
}
