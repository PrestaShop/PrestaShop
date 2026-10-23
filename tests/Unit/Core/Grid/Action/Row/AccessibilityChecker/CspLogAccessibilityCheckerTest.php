<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Action\Row\AccessibilityChecker;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogAllowAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogWeakeningAllowAccessibilityChecker;

/**
 * The violations grid lists only not-yet-allowed sources, so each row offers exactly one of the two
 * "Allow" actions: the plain one for safe sources, the confirm-guarded one for weakening sources.
 */
class CspLogAccessibilityCheckerTest extends TestCase
{
    /**
     * @dataProvider provideRecords
     */
    public function testExactlyOneAllowActionIsGranted(int $isWeakening, bool $allow, bool $weakeningAllow): void
    {
        $record = ['is_weakening' => $isWeakening];

        $this->assertSame($allow, (new CspLogAllowAccessibilityChecker())->isGranted($record));
        $this->assertSame($weakeningAllow, (new CspLogWeakeningAllowAccessibilityChecker())->isGranted($record));
    }

    public static function provideRecords(): iterable
    {
        //                 is_weakening, allow, weakeningAllow
        yield 'safe' => [0, true, false];
        yield 'weakening' => [1, false, true];
    }
}
