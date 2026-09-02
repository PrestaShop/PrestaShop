/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import currencyFormMap from './currency-form-map';
import CurrencyForm from './currency-form';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents(['TranslatableInput']);
  const choiceTree = new window.prestashop.component.ChoiceTree(
    currencyFormMap.shopAssociationTree,
  );
  choiceTree.enableAutoCheckChildren();
  const currencyForm = new CurrencyForm(currencyFormMap);
  currencyForm.init();
});
