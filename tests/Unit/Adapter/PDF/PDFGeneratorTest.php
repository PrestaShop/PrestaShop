<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\PDF;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\PDF\PDFGenerator;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

class PDFGeneratorTest extends TestCase
{
    public function testGeneratePDFUsesTwigPipelineWhenFeatureFlagIsEnabled(): void
    {
        $templateTypeProvider = $this->createMock(PDFTemplateTypeProviderInterface::class);
        $templateTypeProvider->method('getPDFTemplateType')->willReturn('Invoice');

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_PDF_TWIG_RENDERER)
            ->willReturn(true);

        $objects = ['object1', 'object2'];

        $pdfRenderer = $this->createMock(PDFRendererInterface::class);
        $pdfRenderer->expects($this->once())->method('assign')->with('objects', $objects)->willReturn($pdfRenderer);
        $pdfRenderer->expects($this->once())->method('setType')->with('Invoice')->willReturn($pdfRenderer);
        $pdfRenderer->expects($this->once())->method('render')->with(true)->willReturn('%PDF-content%');

        $generator = new PDFGenerator($templateTypeProvider, $featureFlagStateChecker, $pdfRenderer);
        $generator->generatePDF($objects);
    }

    public function testGeneratePDFForResponseRendersWithoutDisplayAndReturnsFilenameWhenFeatureFlagIsEnabled(): void
    {
        $templateTypeProvider = $this->createMock(PDFTemplateTypeProviderInterface::class);
        $templateTypeProvider->method('getPDFTemplateType')->willReturn('CreditSlip');

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')->willReturn(true);

        $pdfRenderer = $this->createMock(PDFRendererInterface::class);
        $pdfRenderer->method('assign')->willReturn($pdfRenderer);
        $pdfRenderer->method('setType')->willReturn($pdfRenderer);
        $pdfRenderer->expects($this->once())->method('render')->with(false)->willReturn('%PDF-content%');
        $pdfRenderer->method('getFilename')->willReturn('credit-slip.pdf');

        $generator = new PDFGenerator($templateTypeProvider, $featureFlagStateChecker, $pdfRenderer);
        $generatedPdf = $generator->generatePDFForResponse(['object1']);

        $this->assertSame('%PDF-content%', $generatedPdf->getContent());
        $this->assertSame('credit-slip.pdf', $generatedPdf->getFileName());
    }
}
