<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Creates a fresh {@see PDFRendererEngineInterface} instance for one PDF render.
 *
 * The underlying engine (TCPDF) is single-use and stateful, so a new instance
 * is created per render() call rather than reused as a long-lived service.
 */
interface PDFRendererEngineFactoryInterface
{
    public function createEngine(string $orientation = 'P'): PDFRendererEngineInterface;
}
