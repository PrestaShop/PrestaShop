<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Service;

use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

interface ShipmentMergerInterface
{
    /**
     * @param ShipmentProduct[] $productsToMove
     */
    public function merge(
        Shipment $source,
        Shipment $target,
        array $productsToMove
    ): void;
}
