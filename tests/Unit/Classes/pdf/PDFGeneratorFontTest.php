<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Classes\pdf;

use PDFGeneratorCore;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The invoice font is picked from the language iso code alone. Languages with no entry fall back
 * to a core font, which is limited to cp1252, so their alphabet comes out as question marks.
 */
class PDFGeneratorFontTest extends TestCase
{
    private const FONT_DIR = _PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/fonts/';

    /**
     * One characteristic letter per language, so a failure names the character that would break.
     *
     * @dataProvider getShippedLanguagesWithACharacterOutsideCp1252
     */
    public function testAShippedLanguageIsMappedToAFontThatCanEncodeIt(string $isoCode, int $codePoint): void
    {
        $font = $this->getFontForLang($isoCode);

        $this->assertNotSame(
            PDFGeneratorCore::DEFAULT_FONT,
            $font,
            sprintf('"%s" falls back to the core font, which cannot encode U+%04X', $isoCode, $codePoint)
        );
        $this->assertTrue(
            $this->fontCovers($font, $codePoint),
            sprintf('"%s" is mapped to "%s", which has no glyph for U+%04X', $isoCode, $font, $codePoint)
        );
    }

    public function getShippedLanguagesWithACharacterOutsideCp1252(): array
    {
        return [
            'Hungarian o double acute' => ['hu', 0x0151],
            'Bosnian c caron' => ['bs', 0x010D],
            'Hindi devanagari a' => ['hi', 0x0905],
            'Bengali a' => ['bn', 0x0985],
            'Russian a' => ['ru', 0x0410],
            'Japanese hiragana a' => ['ja', 0x3042],
            'Turkish g breve' => ['tr', 0x011F],
            'Romanian s comma' => ['ro', 0x0219],
        ];
    }

    private function getFontForLang(string $isoCode): string
    {
        $generator = (new ReflectionClass(PDFGeneratorCore::class))->newInstanceWithoutConstructor();
        $map = (new ReflectionProperty(PDFGeneratorCore::class, 'font_by_lang'))->getValue($generator);

        return $map[$isoCode] ?? PDFGeneratorCore::DEFAULT_FONT;
    }

    private function fontCovers(string $font, int $codePoint): bool
    {
        $definition = self::FONT_DIR . $font . '.php';
        if (!file_exists($definition)) {
            $this->fail(sprintf('No TCPDF definition file for font "%s"', $font));
        }

        $cw = null;
        include $definition;

        return is_array($cw) && array_key_exists($codePoint, $cw);
    }
}
