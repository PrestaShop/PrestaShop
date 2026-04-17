<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Service;

use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

interface ShipmentSplitterInterface
{
    /**
     * @param ShipmentProduct[] $productsToMove
     */
    public function split(
        Shipment $source,
        int $carrierId,
        array $productsToMove
    ): Shipment;
}
