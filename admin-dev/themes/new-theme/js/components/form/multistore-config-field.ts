/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';
import initContextualNotification from '@components/contextual-notification';

const {$} = window;

export default class MultistoreConfigField {
  constructor() {
    this.updateMultistoreFieldOnChange();
    initContextualNotification('checkbox');
  }

  updateMultistoreFieldOnChange(): void {
    $(document).on('change', ComponentsMap.multistoreCheckbox, function () {
      const input = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.inputNotCheckbox);
      const inputContainer = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.inputContainer);
      const labelContainer = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.formControlLabel);
      const isChecked = $(this).is(':checked');
      inputContainer.toggleClass('disabled', !isChecked);
      labelContainer.toggleClass('disabled', !isChecked);
      input.prop('disabled', !isChecked);
    });
  }
}
