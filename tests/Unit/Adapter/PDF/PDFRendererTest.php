<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use Context;
use Language;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\PDFRenderer;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineFactoryInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineInterface;

class PDFRendererTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $language = $this->getMockBuilder(Language::class)->disableOriginalConstructor()->getMock();
        $language->iso_code = 'en';

        $context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $context->language = $language;

        Context::setInstanceForTesting($context);
    }

    protected function tearDown(): void
    {
        Context::deleteTestingInstance();

        parent::tearDown();
    }

    public function testRenderThrowsWhenNoDocumentBuilderSupportsType(): void
    {
        $unsupportedBuilder = $this->createMock(PdfDocumentBuilderInterface::class);
        $unsupportedBuilder->method('supports')->willReturn(false);

        $renderer = new PDFRenderer([$unsupportedBuilder], $this->createMock(PDFRendererEngineFactoryInterface::class));
        $renderer->assign('objects', ['anObject'])->setType('Invoice');

        $this->expectException(PdfException::class);
        $renderer->render();
    }

    public function testRenderThrowsWhenObjectCollectionIsEmpty(): void
    {
        $builder = $this->createMock(PdfDocumentBuilderInterface::class);
        $builder->method('supports')->willReturn(true);

        $renderer = new PDFRenderer([$builder], $this->createMock(PDFRendererEngineFactoryInterface::class));
        $renderer->assign('objects', [])->setType('Invoice');

        $this->expectException(PdfException::class);
        $renderer->render();
    }

    public function testRenderDrivesEngineInTheOrderTcpdfRequiresAndReturnsItsOutput(): void
    {
        $document = $this->createMock(PdfDocumentInterface::class);
        $document->method('getHeader')->willReturn('<header>');
        $document->method('getFooter')->willReturn('<footer>');
        $document->method('getPagination')->willReturn('<pagination>');
        $document->method('getContent')->willReturn('<content>');
        $document->method('getFilename')->willReturn('invoice.pdf');

        $builder = $this->createMock(PdfDocumentBuilderInterface::class);
        $builder->method('supports')->willReturn(true);
        $builder->expects($this->once())->method('build')->with('anObject', false)->willReturn($document);

        $engine = $this->createMock(PDFRendererEngineInterface::class);
        $callOrder = [];
        foreach (['setFontForLanguage', 'startNewPageGroup', 'createHeader', 'createPagination', 'createContent', 'writePage', 'createFooter'] as $method) {
            $engine->expects($this->once())->method($method)->willReturnCallback(function () use (&$callOrder, $method) {
                $callOrder[] = $method;
            });
        }
        $engine->expects($this->once())
            ->method('outputPdf')
            ->with('invoice.pdf', true)
            ->willReturn('%PDF-bytes%');

        $engineFactory = $this->createMock(PDFRendererEngineFactoryInterface::class);
        $engineFactory->method('createEngine')->willReturn($engine);

        $renderer = new PDFRenderer([$builder], $engineFactory);
        $result = $renderer->assign('objects', ['anObject'])->setType('Invoice')->render(true);

        $this->assertSame('%PDF-bytes%', $result);
        $this->assertSame(
            ['setFontForLanguage', 'startNewPageGroup', 'createHeader', 'createPagination', 'createContent', 'writePage', 'createFooter'],
            $callOrder,
            'the footer must be set after writePage(), otherwise TCPDF renders it on the next page group'
        );
        $this->assertSame('invoice.pdf', $renderer->getFilename());
    }
}
