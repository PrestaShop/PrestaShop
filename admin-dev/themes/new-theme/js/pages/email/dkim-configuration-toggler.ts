/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EmailPageMap from '@pages/email/EmailPageMap';

const {$} = window;

/**
 * Class DkimConfigurationToggler is responsible for showing/hiding DKIM configuration form
 */
class DkimConfigurationToggler {
  constructor() {
    $(EmailPageMap.dkimEnableRadio).on('change', (event) => {
      const dkimEnable = Number($(event.currentTarget).val());
      $(EmailPageMap.dkimConfigurationBlock).toggleClass('d-none', dkimEnable === 0);
    });
  }
}

export default DkimConfigurationToggler;
