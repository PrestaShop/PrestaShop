<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Class KpiRowFactory builds a KPI row, able to dispatch a hook.
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
