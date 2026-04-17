<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Hook;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    public function testIsDisplayHookNameDisplayHooksStartWithDisplay(): void
    {
        $this->assertTrue(Hook::isDisplayHookName('displaySomething'));
    }

    public function testIsDisplayHookNameDisplayHooksCannotStartWithAction(): void
    {
        $this->assertFalse(Hook::isDisplayHookName('actionDoWeirdStuff'));
    }

    public function testIsDisplayHookNameHeaderIsNotADisplayHook(): void
    {
        $this->assertFalse(Hook::isDisplayHookName('header'));
    }
}
