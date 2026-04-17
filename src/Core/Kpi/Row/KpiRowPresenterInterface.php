<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

/**
 * Interface KpiRowPresenterInterface describes a KPI row presenter.
 */
interface KpiRowPresenterInterface
{
    /**
     * @param KpiRowInterface $kpiRow
     *
     * @return array
     */
    public function present(KpiRowInterface $kpiRow);
}
