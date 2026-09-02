<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PDFGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Invoices used to embed the whole font face, which made them 462 KB in a language mapped to dejavusans and
 * 1.9 MB in one mapped to freeserif, while a language falling back to helvetica stayed at 7 KB because a core
 * font is never embedded. Only the glyphs actually used are embedded now.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/32913
 */
class PdfGeneratorFontSubsettingTest extends TestCase
{
    private const BODY = '<h1>Invoice</h1><p>Order reference ABCDEFG, total 42.00 EUR.</p>';

    /**
     * The font programs TCPDF embeds live next to their definitions. A document that embeds a whole face is
     * necessarily bigger than the face itself, so comparing against the file on disk keeps this honest
     * without pinning a byte count that a font update would invalidate.
     *
     * @dataProvider provideEmbeddedFontLanguages
     */
    public function testOnlyTheUsedGlyphsAreEmbedded(string $isoCode, string $expectedFont): void
    {
        $fontProgram = _PS_ROOT_DIR_ . '/vendor/tecnickcom/tcpdf/fonts/' . $expectedFont . '.z';
        $this->assertFileExists($fontProgram, 'The font this language maps to is expected to be embeddable.');

        $pdf = new PDFGenerator(false, 'P');
        $pdf->setFontForLang($isoCode);
        $pdf->AddPage();
        $pdf->writeHTML(self::BODY);
        $size = strlen($pdf->Output('test.pdf', 'S'));

        $this->assertLessThan(
            filesize($fontProgram),
            $size,
            sprintf(
                'A one-line %s document came out at %d bytes, which is bigger than the %s font program itself'
                . ' (%d bytes) — the whole face is being embedded instead of the glyphs in use.',
                $isoCode,
                $size,
                $expectedFont,
                filesize($fontProgram)
            )
        );
    }

    public function provideEmbeddedFontLanguages(): array
    {
        return [
            'dejavusans' => ['en', 'dejavusans'],
            'freeserif' => ['el', 'freeserif'],
        ];
    }

    /**
     * A language with no entry in the map falls back to a TCPDF core font, which is never embedded. That is
     * unchanged, and it is the baseline the two cases above are measured against.
     */
    public function testACoreFontLanguageEmbedsNothing(): void
    {
        $pdf = new PDFGenerator(false, 'P');
        $pdf->setFontForLang('de');
        $pdf->AddPage();
        $pdf->writeHTML(self::BODY);

        $this->assertLessThan(50000, strlen($pdf->Output('test.pdf', 'S')));
    }
}
