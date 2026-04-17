<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CreditSlip;

use PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

/**
 * Provides credit slip PDF template type.
 */
final class CreditSlipTemplateTypeProvider implements PDFTemplateTypeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPDFTemplateType()
    {
        return PDF::TEMPLATE_ORDER_SLIP;
    }
}
