/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TaxRulesGroupFormMap from '@pages/tax-rules-group/form/tax-rules-group-form-map';

const {$} = window;

$(() => {
  new window.prestashop.component.ChoiceTree(TaxRulesGroupFormMap.taxRulesGroupShopAssociationInput).enableAutoCheckChildren();
});
