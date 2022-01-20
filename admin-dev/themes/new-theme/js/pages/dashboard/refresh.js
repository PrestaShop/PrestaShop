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

import dashboardMap from '@pages/dashboard/dashboardMap';

function refreshDashboard(moduleName, usePush, extra) {
  const moduleList = [];
  const getWidget = (module) => {
    $.ajax({
      url: $(dashboardMap.mainDiv).data('refreshUrl'),
      data: {
        module,
        dashboard_use_push: Number(usePush),
        extra,
      },
      // Ensure to get fresh data
      headers: {
        'cache-control': 'no-cache',
      },
      cache: false,
      global: false,
      dataType: 'json',
      success: (widgets) => {
        Object.keys(widgets).forEach((widgetName) => {
          Object.keys(widgets[widgetName]).forEach((dataType) => {
            window[dataType](widgetName, widgets[widgetName][dataType]);
          });
        });

        // eslint-disable-next-line radix
        if (parseInt($(dashboardMap.mainDiv).data('dashboardUsePush')) === 1) {
          refreshDashboard(false, false);
        }
      },
      contentType: 'application/json',
    });
  };

  if (moduleName === false) {
    $(dashboardMap.widgets).each((_, element) => {
      moduleList.push($(element).attr('id'));
      if (!usePush) {
        $(element).addClass('loading');
      }
    });
  } else {
    moduleList.push(moduleName);
    if (!usePush) {
      $(dashboardMap.module(moduleName)).addClass('loading');
      //.each((_, element) => {
      //  console.log(element);
      //  $(element).addClass('loading');
      //});
    }
  }

  moduleList.forEach((module) => {
    if (usePush && !$(dashboardMap.module(module)).hasClass('allow_push')) {
      return;
    }

    getWidget(module);
  });
}

export default refreshDashboard;
