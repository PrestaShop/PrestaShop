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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use ConfigurationKPI;
use Context;
use HelperKpi;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;

class AverageResponseTimeKpi implements KpiInterface
{

    public function render()
    {
        /** @var Context $context */
        $context = Context::getContext();
        $translator = $context->getTranslator();
        $time = time();

        $helper = new HelperKpi();
        $helper->id = 'box-age';
        $helper->icon = 'watch';
        $helper->color = 'color2';
        $helper->title = $translator->trans('Average Response Time', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $translator->trans('30 days', [],'Admin.Global');
        if (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME') !== false) {
            $helper->value = ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME');
        }
        $helper->source = $context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=avg_msg_response_time';
        $helper->refresh = (bool) (ConfigurationKPI::get('AVG_MSG_RESPONSE_TIME_EXPIRE') < $time);

        return $helper->generate();
    }
}
