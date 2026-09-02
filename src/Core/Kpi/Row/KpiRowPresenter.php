<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Class KpiRowPresenter presents a KPI row.
 */
final class KpiRowPresenter implements KpiRowPresenterInterface
{
    /**
     * {@inheritdoc}
     */
    public function present(KpiRowInterface $kpiRow)
    {
        $renderedKpis = [];

        /** @var KpiInterface $kpi */
        foreach ($kpiRow->getKpis() as $kpi) {
            $renderedKpis[] = $kpi->render();
        }

        return [
            'kpis' => $renderedKpis,
            'allowRefresh' => $kpiRow->isRefreshAllowed(),
        ];
    }
}
