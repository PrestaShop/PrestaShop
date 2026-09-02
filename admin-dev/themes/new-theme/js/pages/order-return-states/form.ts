/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import initColorPickers from '@app/utils/colorpicker';

const {$} = window;

$(() => {
  initColorPickers();
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
});
