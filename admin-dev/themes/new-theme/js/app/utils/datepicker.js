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

/**
 * Initializes all datetimepickers.
 *
 * 'data-format' attribute is optional to define custom date-time format.
 * If provided format contains time, the time picker appears aside date picker calendar.
 * Default datepicker icons are overridden in css by appending context to corresponding class.
 *
 * Example usage in template:
 *
 * <div class="input-group datepicker">      // .datepicker class is used to select datetimepicker object
 *   <input type="text" class="form-control"
 *     data-format="YYYY-MM-DD HH:mm:ss"     // provide data-format attr in case you need custom format
 *   />
 * </div>
 */
const init = function initDatePickers() {
  const datePickers = $('.datepicker input[type="text"]');

  datePickers.each((key, picker) => {
    $(picker).datetimepicker({
      locale: global.full_language_code,
      format: $(picker).data('format') ? $(picker).data('format') : 'YYYY-MM-DD',
      sideBySide: true,
      icons: {
        up: 'up',
        down: 'down',
        date: 'date',
        time: 'time',
      },
    });
  })
  .on('dp.show', replaceDatePicker)
  .on('dp.hide', function() {
    $(window).off('resize', replaceDatePicker);
  });

  function replaceDatePicker() {
    const datepicker = $('body').find('.bootstrap-datetimepicker-widget');
    if (datepicker.length <= 0) {
      return;
    }

    const position = datepicker.offset(),
      originalHeight = datepicker.outerHeight(),
      margin = (datepicker.outerHeight(true) - datepicker.outerHeight()) / 2
    ;

    // Move datepicker to the exact same place it was but attached to body
    datepicker.appendTo('body');

    //Height changed because the css from column-filters is not applied any more
    const top = position.top + originalHeight - margin - datepicker.outerHeight();

    datepicker.css({
      position: 'absolute',
      top: top,
      bottom: 'auto',
      left: position.left,
      right: 'auto'
    });

    $(window).on('resize', replaceDatePicker);
  }
};

export default init;
