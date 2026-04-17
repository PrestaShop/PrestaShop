<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Interface KpiRowInterface describes a KPI row.
 */
interface KpiRowInterface
{
    /**
     * Add a KPI to this row.
     *
     * @param KpiInterface $kpi
     */
    public function addKpi(KpiInterface $kpi);

    /**
     * @return array[KpiInterface]
     */
    public function getKpis();

    /**
     * @param bool $allowRefresh
     */
    public function setAllowRefresh($allowRefresh);

    /**
     * @return bool
     */
    public function isRefreshAllowed();

    /**
     * @return array
     */
    public function getOptions();
}
