<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

/**
 * Interface KpiRowFactoryInterface describes a KPI row factory.
 */
interface KpiRowFactoryInterface
{
    /**
     * Builds a KPI row.
     *
     * @return KpiRowInterface
     */
    public function build();
}
