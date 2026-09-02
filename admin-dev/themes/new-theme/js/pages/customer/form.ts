/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CustomerForm from './CustomerForm';

$(() => {
  new CustomerForm();

  window.prestashop.component.initComponents(
    [
      'ChoiceTable',
    ],
  );
});
