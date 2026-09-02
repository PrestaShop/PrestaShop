<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Service\Hook;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Hook\HookFinder;

class HookFinderTest extends TestCase
{
    public function testAddOneExpectedClass()
    {
        $finder = new HookFinder();
        $finder->addExpectedInstanceClasses(__CLASS__);

        $this->assertSame(
            [__CLASS__],
            $finder->getExpectedInstanceClasses()
        );
    }

    public function testAddSeveralExpectedClass()
    {
        $finder = new HookFinder();
        $finder->addExpectedInstanceClasses(__CLASS__);
        $finder->addExpectedInstanceClasses([HookFinder::class, TestCase::class]);

        $this->assertSame(
            [__CLASS__, HookFinder::class, TestCase::class],
            $finder->getExpectedInstanceClasses()
        );
    }
}
