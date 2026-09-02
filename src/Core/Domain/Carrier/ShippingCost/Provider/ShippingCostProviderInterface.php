<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

/**
 * Marker interface for all shipping cost data providers.
 * Each provider is responsible for a single business concern
 * (zone resolution, carrier data, free shipping thresholds, range cost, tax rate).
 */
interface ShippingCostProviderInterface
{
}
