<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Hook;
use PHPUnit\Framework\TestCase;
use PrestaShopException;

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

    /**
     * $id_module is a module ID: exec() compares it to the registration's id_module. A module name has
     * been rejected here since the argument check was introduced, so the PHPDoc offering string was wrong.
     */
    public function testExecRejectsAModuleNameAsIdModule(): void
    {
        $this->expectException(PrestaShopException::class);

        // Passing a module name is the defect under test; the documented signature makes it a static error.
        // @phpstan-ignore-next-line
        Hook::exec('displayHeader', [], 'ps_mymodule');
    }

    /**
     * Control for the test above: the check is about being an ID, not about the PHP type, so a numeric
     * string passes it just like an int does.
     */
    public function testExecAcceptsAModuleIdAsIntOrNumericString(): void
    {
        foreach ([1, '1'] as $idModule) {
            try {
                Hook::exec('displayHeader', [], $idModule);
            } catch (PrestaShopException $e) {
                $this->fail(sprintf(
                    'A module ID (%s) must be accepted, got: %s',
                    var_export($idModule, true),
                    $e->getMessage()
                ));
            }
        }

        $this->addToAssertionCount(1);
    }
}
