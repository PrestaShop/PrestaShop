<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use Address;
use Country;
use Order;

final class TrackingUrlFormatter
{
    private const PLACEHOLDERS = ['{country_iso}', '{order_id}', '{order_reference}', '{postcode}'];

    public static function format(?string $trackingUrl, ?string $trackingNumber, Order $order): string
    {
        $trackingUrl = (string) $trackingUrl;
        $replacements = [
            // Not URL-encoded, for backward compatibility
            '@' => (string) $trackingNumber,
            '{order_id}' => (string) $order->id,
            '{order_reference}' => rawurlencode((string) $order->reference),
        ];

        if (str_contains($trackingUrl, '{postcode}') || str_contains($trackingUrl, '{country_iso}')) {
            $address = new Address((int) $order->id_address_delivery);
            $replacements['{postcode}'] = rawurlencode((string) $address->postcode);
            $replacements['{country_iso}'] = (string) Country::getIsoById((int) $address->id_country);
        }

        return strtr($trackingUrl, $replacements);
    }

    /**
     * Lets URL validators check a tracking URL that contains placeholders.
     */
    public static function fillWithSampleValues(string $trackingUrl): string
    {
        return strtr($trackingUrl, array_fill_keys(self::PLACEHOLDERS, '1'));
    }
}
