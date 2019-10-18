<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use ConfigurationKPI;
use Context;
use HelperKpi;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

/**
 * @internal
 */
final class ConversionRateKpi implements KpiInterface
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $translator = Context::getContext()->getTranslator();

        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'show_chart';
        $helper->color = 'color1';
        $helper->title = $translator->trans('Conversion Rate', [], 'Admin.Global');
        $helper->subtitle = $translator->trans('30 days', [], 'Admin.Global');

        if (ConfigurationKPI::get('CONVERSION_RATE') !== false) {
            $helper->value = ConfigurationKPI::get('CONVERSION_RATE');
        }

        if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }

        $helper->source = Context::getContext()->link->getAdminLink('AdminStats')
            . '&ajax=1&action=getKpi&kpi=conversion_rate';
        $helper->refresh = (bool) (ConfigurationKPI::get('CONVERSION_RATE_EXPIRE') < time());

        return $helper->generate();
    }
}
