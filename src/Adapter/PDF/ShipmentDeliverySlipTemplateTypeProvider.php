<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

/**
 * Provides shipment delivery slip PDF template type.
 */
final class ShipmentDeliverySlipTemplateTypeProvider implements PDFTemplateTypeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPDFTemplateType()
    {
        return PDF::TEMPLATE_SHIPMENT_DELIVERY_SLIP;
    }
}
