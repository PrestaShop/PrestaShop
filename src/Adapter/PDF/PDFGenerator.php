<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\PDF;

use PrestaShop\PrestaShop\Adapter\Entity\PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;
use Smarty;

/**
 * Class PDFManager responsible for PDF generation using legacy code.
 */
final class PDFGenerator implements PDFGeneratorInterface
{
    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var PDFTemplateTypeProviderInterface
     */
    private $templateTypeProvider;

    /**
     * @param Smarty $smarty
     * @param PDFTemplateTypeProviderInterface $templateTypeProvider
     */
    public function __construct(
        Smarty $smarty,
        PDFTemplateTypeProviderInterface $templateTypeProvider
    ) {
        $this->smarty = $smarty;
        $this->templateTypeProvider = $templateTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $objectCollection, $display = true, $filename = null)
    {
        $pdf = new PDF($objectCollection, $this->templateTypeProvider->getPDFTemplateType(), $this->smarty);
        if (null !== $filename) {
            $pdf->filename = $filename;
        }
        $pdf->render($display);
    }
}
