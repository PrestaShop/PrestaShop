<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

interface FreeShippingCriteriaProviderInterface extends ShippingCostProviderInterface
{
    public function getCriteria(ShippingCostPriceInterface $context): FreeShippingCriteria;
}
