<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use PDF;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

/**
 * Generates a PDF for a collection of objects of a given template type (one
 * instance per type, see services/adapter/pdf/generator.yml). Rendered by the
 * Twig-based pipeline when pdf_twig_renderer is enabled, the legacy
 * PDF/HTMLTemplate/TCPDF stack otherwise.
 *
 * This is the single place that switches between the two rendering stacks —
 * every *PdfGenerator adapter (Invoice, CreditSlip, OrderReturn, DeliverySlip,
 * ShipmentDeliverySlip) delegates to a typed instance of this class instead of
 * re-implementing the feature-flag branch itself.
 */
final class PDFGenerator implements PDFGeneratorInterface
{
    public function __construct(
        private readonly PDFTemplateTypeProviderInterface $templateTypeProvider,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly PDFRendererInterface $pdfRenderer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $objectCollection): void
    {
        $this->render($objectCollection, true);
    }

    /**
     * @param mixed[] $objectCollection
     */
    public function generatePDFForResponse(array $objectCollection): GeneratedPdf
    {
        return $this->render($objectCollection, false);
    }

    /**
     * @param mixed[] $objectCollection
     */
    private function render(array $objectCollection, bool $display): GeneratedPdf
    {
        $type = $this->templateTypeProvider->getPDFTemplateType();

        if ($this->isTwigRendererEnabled()) {
            $content = $this->pdfRenderer
                ->assign('objects', $objectCollection)
                ->setType($type)
                ->render($display)
            ;

            return new GeneratedPdf($content, $this->pdfRenderer->getFilename());
        }

        $pdf = new PDF($objectCollection, $type, Context::getContext()->smarty);

        return new GeneratedPdf($pdf->render($display), $pdf->getFilename());
    }

    private function isTwigRendererEnabled(): bool
    {
        return $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PDF_TWIG_RENDERER);
    }
}
