<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Interface PDFRendererInterface defines a PDF renderer: given a collection of
 * objects and a template type (see PDFTemplateTypeProviderInterface), it builds
 * one {@see PdfDocumentInterface} per object via the matching
 * {@see PdfDocumentBuilderInterface} and assembles them into a single PDF.
 */
interface PDFRendererInterface
{
    /**
     * @param mixed $value
     */
    public function assign(string $key, $value): self;

    public function setType(string $type): self;

    /**
     * @param bool $display true: send the PDF to the browser for download; false: return the raw bytes only
     *
     * @return string raw PDF bytes, in both display modes
     */
    public function render(bool $display = true): string;

    /**
     * Filename resolved by the last render() call.
     */
    public function getFilename(): string;
}
