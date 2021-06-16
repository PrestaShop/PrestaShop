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
import refreshDashboard from '@pages/dashboard/refresh';

export function toggleDashConfig(widget) {
  const funcName = `${widget}_toggleDashConfig`;

  if ($(`#${widget} section.dash_config`).hasClass('d-none')) {
    $(`#${widget} section`).not('.dash_config').slideUp(500, () => {
      $(`#${widget} section.dash_config`).fadeIn(500).removeClass('d-none');
      if (window[funcName] !== undefined) {
        window[funcName]();
      }
    });
  } else {
    $(`#${widget} section.dash_config`).slideUp(500, () => {
      $(`#${widget} section`).not('.dash_config').slideDown(500).removeClass('d-none');
      $(`#${widget} section.dash_config`).addClass('d-none');
      if (window[funcName] !== undefined) {
        window[funcName]();
      }
    });
  }
}

export function saveDashConfig(widgetName) {
  $(`section#${widgetName} .form-group`).removeClass('has-errors');
  $(`#${widgetName}_errors`).remove();
  const data = {};
  $.each($(`#${widgetName}`).closest('form').serializeArray(), (_, item) => {
    data[item.name] = item.value;
  });

  const moduleName = widgetName.split('_')[0];

  $.ajax({
    url: $(dashboardMap.mainDiv).data('saveModuleOptionsUrl'),
    data: {
      module: moduleName,
      hook: $(`#${moduleName}`).closest('[id^=hook]').attr('id'),
      ...data,
    },
    method: 'POST',
    dataType: 'json',
    error: (XMLHttpRequest, textStatus) => {
      $.growl.error({
        title: 'TECHNICAL ERROR',
        message: `Details:\nError thrown: ${XMLHttpRequest}\nText status: ${textStatus}`,
      });
    },
    success: (jsonData) => {
      if (!jsonData.has_errors) {
        $(`#${moduleName}`).find('section').not('.dash_config').remove();
        toggleDashConfig(moduleName);
        $(`#${moduleName}`).append($(jsonData.widget_html).find('section').not('.dash_config'));
        refreshDashboard(moduleName);
      } else {
        let errorStr = `<div class="alert alert-danger" id="${widgetName}_errors">`;
        Object.keys(jsonData.errors).forEach((error) => {
          errorStr += `${jsonData.errors[error]}<br/>`;
          $(`#${error}`).closest('.form-group').addClass('has-error');
        });
        errorStr += '</div>';
        $(`section#${widgetName} .card .card-body`).prepend(errorStr);
      }
    },
  });
}

export function bindSubmitDashConfig() {
  $(dashboardMap.configSubmitButton).click((event) => {
    event.preventDefault();
    saveDashConfig(event.currentTarget.closest('section').id);
  });
}

export function bindCancelDashConfig() {
  $(dashboardMap.configCancelButton).on('click', () => {
    toggleDashConfig($(this).closest('section.widget').id);
    return false;
  });
}
