<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Delivery;

use PrestaShop\PrestaShop\Adapter\Entity\PDF;
use PrestaShop\PrestaShop\Core\PDF\PDFTemplateTypeProviderInterface;

final class DeliverySlipTemplateTypeProvider implements PDFTemplateTypeProviderInterface
{
    public function getPDFTemplateType(): string
    {
        return PDF::TEMPLATE_DELIVERY_SLIP;
    }
}
