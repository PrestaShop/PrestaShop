<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Interface PDFRendererEngineInterface defines the low-level engine that turns
 * HTML blocks (header, footer, pagination, content) into the final PDF binary.
 *
 * Orientation and disk-cache usage are engine construction concerns (see
 * PDFRendererEngineFactoryInterface), not runtime setters, because the
 * underlying TCPDF instance requires them upfront.
 *
 * Mirrors the legacy PDFGeneratorCore call sequence on purpose: the footer
 * must be set with createFooter() *after* writePage(), otherwise TCPDF
 * renders it on the next page group instead of the current one.
 */
interface PDFRendererEngineInterface
{
    public function setFontForLanguage(string $isoCode): void;

    /**
     * Starts a new page group so page numbering ({:png:}/{:ptg:} placeholders
     * used in the pagination block) restarts for the next document.
     */
    public function startNewPageGroup(): void;

    public function createHeader(string $header): void;

    public function createFooter(string $footer): void;

    public function createContent(string $content): void;

    public function createPagination(string $pagination): void;

    public function writePage(): void;

    /**
     * @param bool $display true: send the PDF to the browser for download; false: return the raw bytes only
     *
     * @return string raw PDF bytes, in both display modes
     */
    public function outputPdf(string $filename, bool $display): string;
}
