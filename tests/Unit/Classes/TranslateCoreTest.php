<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Translate;

class TranslateCoreTest extends TestCase
{
    /**
     * @dataProvider checkAndReplaceArgsDataProvider
     */
    public function testCheckAndReplaceArgs($expected, $string, $args)
    {
        $this->assertSame($expected, Translate::checkAndReplaceArgs($string, $args));
    }

    public function checkAndReplaceArgsDataProvider()
    {
        return [
            // Positional placeholders are replaced as usual
            ['OK: Hi Bye', 'OK: %1$s %2$s', ['Hi', 'Bye']],
            ['OK: Hi Text2 Bye', 'OK: %1$s Text2 %2$s', ['Hi', 'Bye']],
            ['OK: Hi', 'OK: %s', ['Hi']],
            ['OK: Hi Bye', 'OK: %s %s', ['Hi', 'Bye']],

            // A translation that dropped its placeholders is left untouched
            ['NOK1: 100.00', 'NOK1: 100.00', ['Hi', 'Bye']],
            ['NOK2: 100.00', 'NOK2: 100.00', ['Hi']],
            ['Delivered in 10 days', 'Delivered in 10 days', ['Hi', 'Bye']],
            ['No placeholder left', 'No placeholder left', ['Hi', 'Bye']],

            // Only part of the placeholders was kept
            ['NOK1: Bye', 'NOK1: %2$s', ['Hi', 'Bye']],

            // Named parameters are still replaced by key
            ['Hello Bob', 'Hello %name%', ['%name%' => 'Bob']],
            ['3 items', '%count% items', ['%count%' => '3']],

            // Nothing to replace
            ['Untouched %s', 'Untouched %s', []],
            ['Untouched', 'Untouched', []],
        ];
    }
}
