<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Pdf;

use PHPUnit\Framework\TestCase;

/**
 * TCPDF repeats a <thead> on every page a table spans, but only for a table that is not nested in
 * another one. The product table of every PDF document therefore has to be rendered outside the
 * layout table of the document it belongs to, or its column headers disappear from the second page on.
 */
class PdfProductTableTest extends TestCase
{
    /**
     * @return array<array{string}>
     */
    public function getDocumentTemplates(): array
    {
        return [
            ['delivery-slip.tpl'],
            ['invoice-b2b.tpl'],
            ['invoice.tpl'],
            ['order-return.tpl'],
            ['order-slip.tpl'],
            ['supply-order.tpl'],
        ];
    }

    /**
     * @return array<array{string}>
     */
    public function getProductTemplates(): array
    {
        return [
            ['delivery-slip.product-tab.tpl'],
            ['invoice.product-tab.tpl'],
            ['order-return.product-tab.tpl'],
            ['order-slip.product-tab.tpl'],
            ['shipment-delivery-slip.product-tab.tpl'],
            ['supply-order.product-tab.tpl'],
        ];
    }

    /**
     * @dataProvider getDocumentTemplates
     */
    public function testProductTabIsRenderedOutsideAnyTable(string $template): void
    {
        $content = $this->getTemplateContent($template);
        $position = strpos($content, '{$product_tab}');

        $this->assertIsInt($position, sprintf('%s does not render the product tab.', $template));

        $before = substr($content, 0, $position);
        $depth = preg_match_all('/<table[\s>]/', $before) - preg_match_all('/<\/table>/', $before);

        $this->assertSame(0, $depth, sprintf(
            '%s renders the product tab inside %d table(s). TCPDF does not repeat the header of a nested table.',
            $template,
            $depth
        ));
    }

    /**
     * @dataProvider getProductTemplates
     */
    public function testEveryProductCellCarriesAWidth(string $template): void
    {
        $content = $this->getTemplateContent($template);

        $this->assertStringContainsString('<thead>', $content, sprintf(
            '%s has no header rows to repeat.',
            $template
        ));

        // Without a width of its own a body cell is sized independently of the header, which TCPDF
        // prints as a table of its own once it has to repeat it. Only the cells that carry the
        // "product" class are checked: those are the ones that set the column geometry. A cell
        // spanning several columns declares a colspan instead and is skipped.
        preg_match('/<tbody>(.*)<\/tbody>/s', $content, $body);
        $this->assertNotEmpty($body, sprintf('%s has no product rows.', $template));

        preg_match_all('/<td(?![^>]*\b(?:width|colspan)=)[^>]*class="product[^"]*"[^>]*>/', $body[1], $withoutWidth);

        $this->assertSame([], $withoutWidth[0], sprintf(
            '%s has product cells without a width.',
            $template
        ));
    }

    private function getTemplateContent(string $template): string
    {
        $path = _PS_ROOT_DIR_ . '/pdf/' . $template;
        $content = file_get_contents($path);

        $this->assertIsString($content, sprintf('%s cannot be read.', $path));

        return $content;
    }
}
