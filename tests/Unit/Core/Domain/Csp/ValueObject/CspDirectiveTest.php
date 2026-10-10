<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Csp\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;

/**
 * The string-backed directive enum: case/trim-tolerant construction from a report field, and the
 * difference between the throwing fromString() and the native tryFrom().
 */
class CspDirectiveTest extends TestCase
{
    /**
     * @dataProvider provideNormalizableDirectives
     */
    public function testFromStringNormalizesCaseAndWhitespace(string $input, CspDirective $expected): void
    {
        $this->assertSame($expected, CspDirective::fromString($input));
    }

    /**
     * @return iterable<string, array{string, CspDirective}>
     */
    public function provideNormalizableDirectives(): iterable
    {
        yield 'exact' => ['script-src', CspDirective::SCRIPT_SRC];
        yield 'uppercase' => ['SCRIPT-SRC', CspDirective::SCRIPT_SRC];
        yield 'mixed case' => ['Script-Src', CspDirective::SCRIPT_SRC];
        yield 'surrounding whitespace' => ['  style-src  ', CspDirective::STYLE_SRC];
        yield 'frame-ancestors' => ['frame-ancestors', CspDirective::FRAME_ANCESTORS];
    }

    public function testFromStringThrowsOnUnknownDirective(): void
    {
        $this->expectException(CspConstraintException::class);
        $this->expectExceptionCode(CspConstraintException::INVALID_DIRECTIVE);

        CspDirective::fromString('bogus-directive');
    }

    public function testTryFromReturnsNullOnUnknownDirective(): void
    {
        $this->assertNull(CspDirective::tryFrom('bogus'));
    }

    public function testTryFromReturnsTheCaseForAKnownValue(): void
    {
        $this->assertSame(CspDirective::SCRIPT_SRC, CspDirective::tryFrom('script-src'));
    }

    /**
     * @dataProvider provideCoarsenableDirectives
     */
    public function testCoarsenReducesGranularDirectivesToTheirParent(CspDirective $input, CspDirective $expected): void
    {
        $this->assertSame($expected, $input->coarsen());
    }

    /**
     * @return iterable<string, array{CspDirective, CspDirective}>
     */
    public function provideCoarsenableDirectives(): iterable
    {
        yield 'script-src-elem -> script-src' => [CspDirective::SCRIPT_SRC_ELEM, CspDirective::SCRIPT_SRC];
        yield 'script-src-attr -> script-src' => [CspDirective::SCRIPT_SRC_ATTR, CspDirective::SCRIPT_SRC];
        yield 'style-src-elem -> style-src' => [CspDirective::STYLE_SRC_ELEM, CspDirective::STYLE_SRC];
        yield 'style-src-attr -> style-src' => [CspDirective::STYLE_SRC_ATTR, CspDirective::STYLE_SRC];
        yield 'script-src unchanged' => [CspDirective::SCRIPT_SRC, CspDirective::SCRIPT_SRC];
        yield 'img-src unchanged' => [CspDirective::IMG_SRC, CspDirective::IMG_SRC];
        yield 'object-src unchanged' => [CspDirective::OBJECT_SRC, CspDirective::OBJECT_SRC];
    }
}
