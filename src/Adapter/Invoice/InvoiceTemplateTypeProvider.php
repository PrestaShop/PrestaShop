<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use PrestaShop\PrestaShop\Adapter\Entity\PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

/**
 * Class InvoiceTemplateTypeProvider provides invoice PDF template type.
 */
final class InvoiceTemplateTypeProvider implements PDFTemplateTypeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPDFTemplateType()
    {
        return PDF::TEMPLATE_INVOICE;
    }
}
