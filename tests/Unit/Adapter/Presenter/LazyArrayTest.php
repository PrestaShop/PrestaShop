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
