<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn;

use PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

/**
 * Provides order return PDF template type.
 */
final class OrderReturnTemplateTypeProvider implements PDFTemplateTypeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPDFTemplateType()
    {
        return PDF::TEMPLATE_ORDER_RETURN;
    }
}
