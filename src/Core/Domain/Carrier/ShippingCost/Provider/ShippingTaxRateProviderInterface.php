<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

/**
 * Provides the applicable tax rate for a given carrier and delivery address.
 */
interface ShippingTaxRateProviderInterface extends ShippingCostProviderInterface
{
    public function getTaxRate(int $carrierId, int $addressId): TaxRate;
}
