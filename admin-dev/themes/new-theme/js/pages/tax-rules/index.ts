/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';
import TaxRulesManager from '@pages/tax-rules/tax-rules-manager';

document.addEventListener('DOMContentLoaded', () => {
  new FormSubmitButton();
  new TaxRulesManager();
});
