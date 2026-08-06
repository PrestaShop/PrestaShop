<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

/**
 * Formats the delivery slip number for shipment-based delivery slips.
 *
 * The canonical format is: {prefix}{orderId:06d}-{shipmentId}
 * Example: "DE000042-7"
 */
final class DeliverySlipNumber
{
    public const NUMBER_FORMAT = '%s%06d-%d';

    /**
     * Returns the formatted delivery slip number for a shipment.
     *
     * @param string $prefix Localized delivery prefix (PS_DELIVERY_PREFIX)
     * @param int $orderId The order ID, zero-padded to 6 digits
     * @param int $shipmentId The shipment ID appended after a dash
     */
    public static function format(string $prefix, int $orderId, int $shipmentId): string
    {
        return sprintf(self::NUMBER_FORMAT, $prefix, $orderId, $shipmentId);
    }
}
