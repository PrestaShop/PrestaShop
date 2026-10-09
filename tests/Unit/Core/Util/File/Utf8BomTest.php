<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Util\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\File\Utf8Bom;

class Utf8BomTest extends TestCase
{
    public function testItIsTheThreeByteUtf8Signature(): void
    {
        $this->assertSame("\xEF\xBB\xBF", Utf8Bom::SEQUENCE);
        $this->assertSame(3, strlen(Utf8Bom::SEQUENCE));
    }

    public function testItSkipsALeadingBom(): void
    {
        $handle = $this->handleContaining(Utf8Bom::SEQUENCE . "first;second\nrow\n");

        Utf8Bom::skip($handle);

        $this->assertSame("first;second\n", fgets($handle));
    }

    /**
     * The reason skip() rewinds rather than seeking forward unconditionally: most files
     * have no BOM, and swallowing their first three bytes would silently corrupt the
     * header row instead of the encoding.
     */
    public function testItLeavesAFileWithoutABomUntouched(): void
    {
        $handle = $this->handleContaining("first;second\nrow\n");

        Utf8Bom::skip($handle);

        $this->assertSame("first;second\n", fgets($handle));
    }

    public function testItDoesNotSwallowBytesThatOnlyLookLikeABom(): void
    {
        $handle = $this->handleContaining("\xEF\xBBfirst\n");

        Utf8Bom::skip($handle);

        $this->assertSame("\xEF\xBBfirst\n", fgets($handle));
    }

    public function testItIsSafeToCallOnAHandleThatHasAlreadyBeenRead(): void
    {
        $handle = $this->handleContaining(Utf8Bom::SEQUENCE . "first;second\n");
        fgets($handle);

        Utf8Bom::skip($handle);

        $this->assertSame("first;second\n", fgets($handle));
    }

    public function testItIgnoresANonResource(): void
    {
        $this->expectNotToPerformAssertions();

        Utf8Bom::skip(null);
        Utf8Bom::skip('not a handle');
    }

    /**
     * @return resource
     */
    private function handleContaining(string $contents)
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        return $handle;
    }
}
