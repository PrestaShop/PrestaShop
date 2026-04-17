/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ZipCodeManager from '@pages/country/components/zip-code-manager';
import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );

  new FormSubmitButton();
  new ZipCodeManager();
});
