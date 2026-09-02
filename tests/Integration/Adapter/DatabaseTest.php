<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Database;

class DatabaseTest extends TestCase
{
    public function providerEscape(): iterable
    {
        yield ['hello', 'hello'];
        yield ['\\\'inject', '\'inject'];
        yield ['\\"inject', '"inject'];
        yield [42, 42];
        yield [4.2, 4.2];
        yield ['4\\\'200', '4\'200'];
    }

    /**
     * @dataProvider providerEscape
     */
    public function testValuesAreEscaped($expected, $actual): void
    {
        $db = new Database();
        $this->assertEquals($expected, $db->escape($actual));
    }
}
