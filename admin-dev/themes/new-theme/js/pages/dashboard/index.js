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
import getBlogRss from '@pages/dashboard/news';
import refreshDashboard from '@pages/dashboard/refresh';
import * as data from '@pages/dashboard/data';
import {bindCancelDashConfig, bindSubmitDashConfig, toggleDashConfig} from '@pages/dashboard/configuration';

window.data_value = data.dataValue;
window.data_trends = data.dataTrends;
window.data_list_small = data.dataListSmall;
window.data_chart = data.dataChart;
window.data_table = data.dataTable;

const {$} = window;

$(() => {
  if (!$(dashboardMap.mainDiv).data('demoMode')) {
    $(dashboardMap.demoButton).removeClass('btn-primary').addClass('btn-outline-primary');
    $(dashboardMap.demoButtonIcon).text('toggle_off');
  }

  $(dashboardMap.fromDatePicker).datetimepicker({
    format: 'YYYY-MM-DD',
  });

  $(dashboardMap.demoButton).click((event) => {
    event.preventDefault();
    $.ajax({
      url: $(dashboardMap.mainDiv).data('simulationUrl'),
      data: {
        simulation: $(dashboardMap.demoButton).hasClass('btn-primary') ? 0 : 1,
      },
      success: () => {
        if ($(dashboardMap.demoButton).hasClass('btn-primary')) {
          $(dashboardMap.demoButton).removeClass('btn-primary').addClass('btn-outline-primary');
          $(dashboardMap.demoButtonIcon).text('toggle_off');
          $(dashboardMap.mainDiv).data('demoMode', 0);
        } else {
          $(dashboardMap.demoButton).removeClass('btn-outline-primary').addClass('btn-primary');
          $(dashboardMap.demoButtonIcon).text('toggle_on');
          $(dashboardMap.mainDiv).data('demoMode', 1);
        }
        refreshDashboard(false, false);
      },
    });
  });

  $(dashboardMap.refreshButton).click((event) => {
    event.preventDefault();
    refreshDashboard(event.currentTarget.closest('section').id);
  });

  $(dashboardMap.dashConfigButton).click((event) => {
    toggleDashConfig(event.currentTarget.closest('section').id);
    return false;
  });

  getBlogRss();
  refreshDashboard(false, false);
  bindCancelDashConfig();
  bindSubmitDashConfig();
});
