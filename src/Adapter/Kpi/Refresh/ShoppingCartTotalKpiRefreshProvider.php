<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Cart;
use Currency;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception\KpiValueUnavailableException;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use Validate;

/**
 * Computes the refreshed value for the "Shopping Cart Total" KPI.
 *
 * Unlike every other KPI, this one is not cached via the KPI configuration: it is a pure,
 * on-demand read of a single cart's total, matching the legacy behavior.
 */
class ShoppingCartTotalKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly LocaleInterface $locale
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $cartId = (int) ($requestParameters['cartId'] ?? 0);
        $cart = $cartId > 0 ? new Cart($cartId) : null;
        if (null === $cart || !Validate::isLoadedObject($cart)) {
            throw new KpiValueUnavailableException(sprintf('Cart %d not found', $cartId));
        }

        $value = $this->locale->formatPrice(
            $cart->getCartTotalPrice(),
            Currency::getIsoCodeById((int) $cart->id_currency)
        );

        return new KpiRefreshValue((string) $value);
    }
}
