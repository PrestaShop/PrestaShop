<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Kpi\Refresh;

/**
 * Interface KpiRefreshProviderInterface describes a provider able to compute the up-to-date
 * value of a single KPI, for the KPI boxes' asynchronous refresh mechanism.
 */
interface KpiRefreshProviderInterface
{
    /**
     * Computes and returns the current value for this KPI.
     *
     * @param array<string, mixed> $requestParameters extra request parameters a specific KPI may need
     *                                                (e.g. the shopping cart total KPI needs the cart id)
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue;
}
