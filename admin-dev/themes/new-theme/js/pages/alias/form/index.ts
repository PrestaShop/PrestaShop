/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import AliasesCollectionManager from '@pages/alias/components/aliases-collection-manager';
import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  new FormSubmitButton();
  new AliasesCollectionManager();
});
