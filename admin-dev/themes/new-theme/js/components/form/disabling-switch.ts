/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ComponentsMap from '@components/components-map';
import FormFieldToggler from '@components/form/form-field-toggler';

/**
 * This components work along with the form option disabling_switch it automatizes the initialization
 * of the switch disabler inputs, it is accessible easily with the other PrestaShop components via the
 * initComponents method.
 */
export default class DisablingSwitch {
  constructor() {
    new FormFieldToggler({
      disablingInputSelector: ComponentsMap.disablingSwitch.disablingSelector,
    });
  }
}
