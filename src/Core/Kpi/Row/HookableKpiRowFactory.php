<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Class HookableKpiRowFactory builds a KPI row, able to dispatch a hook.
 */
final class HookableKpiRowFactory implements KpiRowFactoryInterface
{
    /**
     * @var KpiInterface[] the list of KPIs to display
     */
    private $kpis;

    /**
     * @var HookDispatcherInterface the Hook Dispatcher
     */
    private $hookDispatcher;

    /**
     * @var string used to make the hook selectable
     */
    private $identifier;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param KpiInterface[] $kpis
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $identifier
     */
    public function __construct(
        array $kpis,
        HookDispatcherInterface $hookDispatcher,
        $identifier
    ) {
        if ($this->validateKpis($kpis) && $this->validateIdentifier($identifier)) {
            $this->kpis = $kpis;
            $this->hookDispatcher = $hookDispatcher;
            $this->identifier = $identifier;
        }
    }

    /**
     * @return KpiRow|void
     *
     * @throws InvalidArgumentException
     */
    public function build()
    {
        $kpiRow = new KpiRow($this->options);

        $this->hookDispatcher->dispatchWithParameters($this->getHookName($this->identifier), [
            'kpis' => &$this->kpis,
        ]);

        if ($this->validateKpis($this->kpis)) {
            foreach ($this->kpis as $kpi) {
                $kpiRow->addKpi($kpi);
            }

            return $kpiRow;
        }
    }

    /**
     * Set options for kpi row
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param array $kpis
     *
     * @return bool true if valid, else throw an exception
     *
     * @throws InvalidArgumentException
     */
    private function validateKpis(array $kpis)
    {
        foreach ($kpis as $kpi) {
            if (!$kpi instanceof KpiInterface) {
                throw InvalidArgumentException::invalidKpi($kpi);
            }
        }

        return true;
    }

    /**
     * @param mixed $identifier
     *
     * @return bool true if valid, else throw an exception
     *
     * @throws InvalidArgumentException
     */
    private function validateIdentifier($identifier)
    {
        if (!is_string($identifier)) {
            throw InvalidArgumentException::invalidIdentifier($identifier);
        }

        return true;
    }

    /**
     * @param string $identifier
     *
     * @return string
     */
    private function getHookName($identifier)
    {
        return 'action' . ucfirst($identifier) . 'KpiRowModifier';
    }
}
