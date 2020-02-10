<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Row;

use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * Class KpiRowFactory builds a KPI row.
 *
 * @deprecated since 1.7.6, will be removed in the next major version, use HookableKpiRowFactory instead.
 */
final class KpiRowFactory implements KpiRowFactoryInterface
{
    /**
     * @var KpiInterface[]
     */
    private $kpis;

    /**
     * @param KpiInterface ...$kpis
     */
    public function __construct(KpiInterface ...$kpis)
    {
        @trigger_error(
            'Using `KpiRowFactory` class is deprecated and will be removed in the next major,' .
            'use HookableKpiRowFactory instead',
            E_USER_DEPRECATED
        );

        $this->kpis = $kpis;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $kpiRow = new KpiRow();

        foreach ($this->kpis as $kpi) {
            $kpiRow->addKpi($kpi);
        }

        return $kpiRow;
    }
}
