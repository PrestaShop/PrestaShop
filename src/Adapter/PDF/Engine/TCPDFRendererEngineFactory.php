<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Engine;

use Configuration;
use Context;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineFactoryInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineInterface;

final class TCPDFRendererEngineFactory implements PDFRendererEngineFactoryInterface
{
    public function createEngine(string $orientation = 'P'): PDFRendererEngineInterface
    {
        return new PDFRendererTCPDF(
            (bool) Configuration::get('PS_PDF_USE_CACHE'),
            $orientation,
            (bool) Context::getContext()->language->is_rtl
        );
    }
}
