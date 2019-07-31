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
