<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Kpi\Refresh;

/**
 * Value object holding a freshly computed KPI value, as returned by the KPI refresh AJAX endpoint.
 */
final class KpiRefreshValue
{
    public function __construct(
        private readonly string $value,
        private readonly ?string $tooltip = null
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }
}
