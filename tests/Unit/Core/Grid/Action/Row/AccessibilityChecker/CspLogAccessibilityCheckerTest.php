<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Action\Row\AccessibilityChecker;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogAllowAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogRevokeAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogWeakeningAllowAccessibilityChecker;

/**
 * The three row-action checkers must be mutually exclusive per row: a source shows exactly one of
 * Allow / Allow-weakening / Revoke.
 */
class CspLogAccessibilityCheckerTest extends TestCase
{
    /**
     * @dataProvider provideRecords
     */
    public function testExactlyTheRightActionIsGranted(int $isAllowed, int $isWeakening, bool $allow, bool $weakeningAllow, bool $revoke): void
    {
        $record = ['is_allowed' => $isAllowed, 'is_weakening' => $isWeakening];

        $this->assertSame($allow, (new CspLogAllowAccessibilityChecker())->isGranted($record));
        $this->assertSame($weakeningAllow, (new CspLogWeakeningAllowAccessibilityChecker())->isGranted($record));
        $this->assertSame($revoke, (new CspLogRevokeAccessibilityChecker())->isGranted($record));
    }

    public static function provideRecords(): iterable
    {
        //                            is_allowed, is_weakening, allow, weakeningAllow, revoke
        yield 'not allowed, safe' => [0, 0, true, false, false];
        yield 'not allowed, weakening' => [0, 1, false, true, false];
        yield 'allowed, safe' => [1, 0, false, false, true];
        yield 'allowed, weakening' => [1, 1, false, false, true];
    }
}
