<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Presenter;

use PHPUnit\Framework\TestCase;

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
}
