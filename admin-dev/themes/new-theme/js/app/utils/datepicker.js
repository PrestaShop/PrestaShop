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
import 'url-polyfill';

const $ = global.$;

const replaceDatePicker = () => {
  const datepickerWidget = $('body').find('.bootstrap-datetimepicker-widget:last');
  if (datepickerWidget.length <= 0) {
    return;
  }

  const position = datepickerWidget.offset();
  const originalHeight = datepickerWidget.outerHeight();
  const margin = (datepickerWidget.outerHeight(true) - originalHeight) / 2;

  // Move datepicker to the exact same place it was but attached to body
  datepickerWidget.appendTo('body');

  // Height changed because the css from column-filters is not applied any more
  let top = (position.top) + margin;

  // Datepicker is settle to the top position
  if (datepickerWidget.hasClass('top')) {
    top += (originalHeight - datepickerWidget.outerHeight(true) - margin);
  }

  datepickerWidget.css({
    position: 'absolute',
    top,
    bottom: 'auto',
    left: position.left,
    right: 'auto',
  });

  $(window).on('resize', replaceDatePicker);
};

/**
 * Enable all datepickers.
 */
const init = function initDatePickers() {
  $('.datepicker input[type="text"]').datetimepicker(
    {
      locale: global.full_language_code,
      format: 'YYYY-MM-DD',
    },
  ).on(
    'dp.show',
    replaceDatePicker,
  ).on(
    'dp.hide',
    () => {
      $(window).off('resize', replaceDatePicker);
    },
  );
};

export default init;
