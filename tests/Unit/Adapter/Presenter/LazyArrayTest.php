<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Presenter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertiesBag;

class LazyArrayTest extends TestCase
{
    public function testBasicConstructAndCall()
    {
        $test = new LazyArrayImplementation();

        $this->assertFalse($test->wasPropertyOneCalled());

        $test->getPropertyOne();

        $this->assertTrue($test->wasPropertyOneCalled());
        $this->assertEquals(1, $test->count());
    }

    public function testAppendArray()
    {
        $test = new LazyArrayImplementation();
        $test->appendArray(['a' => 1]);

        $this->assertEquals(2, $test->count());
        $this->assertEquals(1, $test['a']);
    }

    public function testExtraPropertiesIndexAbsentWithoutBagInitialization()
    {
        $test = new LazyArrayImplementation();

        $this->assertFalse(isset($test['extra_properties']));
        foreach ($test as $key => $value) {
            $this->assertNotSame('extra_properties', $key);
        }
    }

    public function testExtraPropertiesIndexExposedWhenBagIsInitialized()
    {
        $test = new class() extends LazyArrayImplementation {
            public function __construct()
            {
                // Mimics the presenter pattern: bag set before parent::__construct().
                $this->extraPropertiesBag = new ExtraPropertiesBag(static fn (): array => []);
                parent::__construct();
            }
        };

        $this->assertTrue(isset($test['extra_properties']));
        $this->assertInstanceOf(ExtraPropertiesBag::class, $test['extra_properties']);
        $this->assertEquals(2, $test->count());
    }

    public function testBasicAppendClosureArray()
    {
        $counter = 0;

        $test = new LazyArrayImplementation();
        $test->appendClosure('a', function () use ($counter) {
            ++$counter;

            return $counter;
        });

        $this->assertEquals(2, $test->count());

        $this->assertEquals(1, $test['a']);
        // as result is stored in cache, next call does not increment the counter
        $this->assertEquals(1, $test['a']);
    }

    public function testAdvancedAppendClosureArray()
    {
        $dummyLog = new DummyLog();

        $test = new LazyArrayImplementation();
        $test->appendClosure('b', function () use ($dummyLog) {
            $dummyLog->ping();

            return $dummyLog->getPingCounter();
        });

        $this->assertEquals(2, $test->count());
        $this->assertEquals(1, $test['b']);
        // as result is stored in cache, next call does not perform a ping
        $this->assertEquals(1, $test['b']);

        $test->clearMethodCacheResults();
        $this->assertEquals(2, $test['b']);
    }

    public function testCloneSharesComputedValues(): void
    {
        $dummyLog = new DummyLog();
        $test = new LazyArrayImplementation();
        $test->appendClosure('b', function () use ($dummyLog) {
            $dummyLog->ping();

            return $dummyLog->getPingCounter();
        });
        $clone = clone $test;

        $this->assertSame(1, $clone['b']);
        $this->assertSame(1, $test['b']);
        $this->assertSame(1, $dummyLog->getPingCounter());
    }

    public function testValueSetOnCloneDoesNotReachTheOriginal(): void
    {
        $test = new LazyArrayImplementation();
        $test->appendClosure('b', fn () => 'computed');
        $clone = clone $test;

        $this->assertSame('computed', $clone['b']);
        $clone->offsetSet('b', 'filtered', true);

        $this->assertSame('filtered', $clone['b']);
        $this->assertSame('computed', $test['b']);
    }

    public function testClearMethodCacheResultsOnlyClearsTheInstance(): void
    {
        $dummyLog = new DummyLog();
        $test = new LazyArrayImplementation();
        $test->appendClosure('b', function () use ($dummyLog) {
            $dummyLog->ping();

            return $dummyLog->getPingCounter();
        });
        $clone = clone $test;
        $this->assertSame(1, $test['b']);

        $clone->clearMethodCacheResults();

        $this->assertSame(2, $clone['b']);
        $this->assertSame(1, $test['b']);
    }

    public function testClosureRedefinedOnCloneDoesNotReachTheOriginal(): void
    {
        $test = new LazyArrayImplementation();
        $test->appendClosure('b', fn () => 'original');
        $clone = clone $test;

        $clone->appendClosure('b', fn () => 'redefined');

        $this->assertSame('redefined', $clone['b']);
        $this->assertSame('original', $test['b']);
    }
}
