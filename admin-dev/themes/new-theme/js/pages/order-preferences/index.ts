/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TermsAndConditionsOptionHandler from './terms-and-conditions-option-handler';

const {$} = window;

$(() => {
  new TermsAndConditionsOptionHandler();

  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
    ],
  );
});
