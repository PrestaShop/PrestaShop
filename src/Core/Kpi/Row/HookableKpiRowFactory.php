<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

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
     * @param KpiInterface[] $kpis
     */
    public function __construct(
        array $kpis,
        HookDispatcherInterface $hookDispatcher,
        $identifier
    ) {
        $this->kpis = $kpis;
        $this->hookDispatcher = $hookDispatcher;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $kpiRow = new KpiRow();

        $this->hookDispatcher->dispatchWithParameters($this->getHookName($this->identifier), $this->kpis);

        foreach ($this->kpis as $kpi) {
            $kpiRow->addKpi($kpi);
        }

        return $kpiRow;
    }

    /**
     * @param string $identifier
     *
     * @return string
     */
    private function getHookName($identifier)
    {
        return 'action' . ucfirst($identifier) . 'KpiRow';
    }
}
