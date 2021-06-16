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

export function dataValue(widgetName, data) {
  Object.keys(data).forEach((dataId) => {
    $(`#${dataId}`).html(data[dataId]);
    $(`#${dataId}, #${widgetName}`).closest('section').removeClass('loading');
  });
}

export function dataTrends(widgetName, data) {
  Object.keys(data).forEach((dataId) => {
    const el = $(`#${dataId}`);
    el.html(data[dataId].value);
    if (data[dataId].way === 'up') {
      el.parent().removeClass('dash_trend_down').removeClass('dash_trend_right').addClass('dash_trend_up');
    } else if (data[dataId].way === 'down') {
      el.parent().removeClass('dash_trend_up').removeClass('dash_trend_right').addClass('dash_trend_down');
    } else {
      el.parent().removeClass('dash_trend_down').removeClass('dash_trend_up').addClass('dash_trend_right');
    }
    el.closest('section').removeClass('loading');
  });
}

export function dataListSmall(widgetName, data) {
  Object.keys(data).forEach((dataId) => {
    $(`#${dataId}`).html('');
    Object.keys(data[dataId]).forEach((item) => {
      $(`#${dataId}`).append(
        `<li><span class="data_label">${item}</span><span class="data_value size_s"><span>${data[dataId][item]}</span></span></li>`,
      );
    });
    $(`#${dataId} #${widgetName}`).closest('section').removeClass('loading');
  });
}

export function dataChart(widgetName, charts) {
  Object.keys(charts).forEach((chartId) => {
    window[charts[chartId].chart_type](widgetName, charts[chartId]);
  });
}

export function dataTable(widgetName, data) {
  const noResults = $(dashboardMap.mainDiv).data('notFound');
  Object.keys(data).forEach((dataId) => {
    // Fill header
    let tr = '<tr>';
    Object.keys(data[dataId].header).forEach((header) => {
      const head = data[dataId].header[header];
      let th = `<th${(head.class) ? ` class="${head.class}"` : ''}${(head.id ? ` id="${head.id}"` : '')}>`;
      th += (head.wrapper_start ? ` ${head.wrapper_start} ` : '');
      th += head.title;
      th += (head.wrapper_stop ? ` ${head.wrapper_stop} ` : '');
      th += '</th>';
      tr += th;
    });
    tr += '</tr>';
    $(`#${dataId} thead`).html(tr);

    // Fill body
    $(`#${dataId} tbody`).html('');

    if (typeof data[dataId].body === 'string') {
      $(`#${dataId} tbody`).html(
        `<tr><td class="text-center" colspan="${data[dataId].header.length}"><br/>${data[dataId].body}</td></tr>`
      );
    } else if (data[dataId].body.length) {
      Object.keys(data[dataId].body).forEach((bodyContentId) => {
        tr = '<tr>';
        Object.keys(data[dataId].body[bodyContentId]).forEach((bodyContent) => {
          const body = data[dataId].body[bodyContentId][bodyContent];
          let td = `<td${(body.class ? ` class="${body.class}"` : '')}${(body.id ? ` id="${body.id}"` : '')}>`;
          td += (body.wrapper_start ? ` ${body.wrapper_start} ` : '');
          td += body.value;
          td += (body.wrapper_end ? ` ${body.wrapper_end} ` : '');
          td += '</td>';
          tr += td;
        });
        tr += '</tr>';
        $(`#${dataId} tbody`).append(tr);
      });
    } else {
      $(`#${dataId} tbody`).html(
        `<tr><td class="text-center" colspan="${data[dataId].header.length}">${noResults}</td></tr>`
      );
    }
  });
}
