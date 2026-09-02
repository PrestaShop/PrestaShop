<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Class KpiRow defines a row of KPIs.
 */
final class KpiRow implements KpiRowInterface
{
    /**
     * @var bool
     */
    private $allowRefresh = true;

    /**
     * @var array[KpiInterface]
     */
    private $kpis = [];

    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function addKpi(KpiInterface $kpi)
    {
        // setOptions() is optional & not part of interface to avoid BC break
        if (method_exists($kpi, 'setOptions')) {
            $kpi->setOptions($this->options);
        }

        $this->kpis[] = $kpi;
    }

    /**
     * @return array[KpiInterface]
     */
    public function getKpis()
    {
        return $this->kpis;
    }

    /**
     * @param bool $allowRefresh
     */
    public function setAllowRefresh($allowRefresh)
    {
        $this->allowRefresh = $allowRefresh;
    }

    /**
     * @return bool
     */
    public function isRefreshAllowed()
    {
        return $this->allowRefresh;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
