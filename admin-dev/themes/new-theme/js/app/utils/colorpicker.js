/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import 'bootstrap-colorpicker';

const {$} = window;

/**
 * Enable all colorpickers.
 */
const init = function initDatePickers() {
  $('.colorpicker input[type="text"]').each((i, picker) => {
    $(picker).colorpicker();
    $(picker).on('colorpickerCreate', () => {
      $(picker).css('background-color', $(picker).val());
    });
    $(picker).on('colorpickerChange', (event) => {
      $(picker).css('background-color', event.color.toString());
    });
  });
};

export default init;
