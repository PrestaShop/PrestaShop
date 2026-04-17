<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Interface PDFDataProviderInterface describes a PDF template type provider.
 */
interface PDFTemplateTypeProviderInterface
{
    /**
     * Gets PDF template type.
     *
     * @return string
     */
    public function getPDFTemplateType();
}
