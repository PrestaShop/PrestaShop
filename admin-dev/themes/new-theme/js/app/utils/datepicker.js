/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import 'url-polyfill';

const {$} = window;

const replaceDatePicker = () => {
  const datepickerWidget = $('body').find(
    '.bootstrap-datetimepicker-widget:last',
  );

  if (datepickerWidget.length <= 0) {
    return;
  }

  const position = datepickerWidget.offset();
  const originalHeight = datepickerWidget.outerHeight();
  const margin = (datepickerWidget.outerHeight(true) - originalHeight) / 2;

  // Move datepicker to the exact same place it was but attached to body
  datepickerWidget.appendTo('body');

  // Height changed because the css from column-filters is not applied any more
  let top = position.top + margin;

  // Datepicker is settle to the top position
  if (datepickerWidget.hasClass('top')) {
    top += originalHeight - datepickerWidget.outerHeight(true) - margin;
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
  const $datePickers = $('.datepicker input[type="text"]');
  $.each($datePickers, (i, picker) => {
    $(picker)
      .datetimepicker({
        locale: window.full_language_code,
        format: $(picker).data('format')
          ? $(picker).data('format')
          : 'YYYY-MM-DD',
        sideBySide: true,
        icons: {
          time: 'time',
          date: 'date',
          up: 'up',
          down: 'down',
        },
      })
      .on('dp.show', replaceDatePicker)
      .on('dp.hide', () => {
        $(window).off('resize', replaceDatePicker);
      })
      .on('dp.change', (e) => {
        // Looks like we can't bind an event to a datepicker selected afterwhile.
        // So we emit an event on change to manipulate datas
        const event = new CustomEvent('datepickerChange', e);
        window.document.dispatchEvent(event);
      });
  });
};

export default init;
