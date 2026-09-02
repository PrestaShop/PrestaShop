<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Interface PDFGeneratorInterface defines a PDF generator.
 */
interface PDFGeneratorInterface
{
    /**
     * Generates PDF out of given object and template using legacy generator.
     *
     * @param array $objectCollection collection of objects
     */
    public function generatePDF(array $objectCollection);
}
