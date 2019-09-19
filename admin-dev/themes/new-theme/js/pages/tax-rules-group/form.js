/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import ChoiceTree from '../../components/form/choice-tree';
import TaxRulesGroupForm from './TaxRulesGroupForm';
import Grid from '../../components/grid/grid';
import BulkActionCheckboxExtension from '../../components/grid/extension/bulk-action-checkbox-extension';
import SubmitRowActionExtension from '../../components/grid/extension/action/row/submit-row-action-extension';
import SubmitBulkExtension from '../../components/grid/extension/submit-bulk-action-extension';

const $ = window.$;

$(() => {
  const taxRuleGrid = new Grid('tax_rule_grid');

  // needed for shop association input in form
  new ChoiceTree('#tax_rules_group_shop_association').enableAutoCheckChildren();
  taxRuleGrid.addExtension(new BulkActionCheckboxExtension());
  taxRuleGrid.addExtension(new SubmitBulkExtension());
  taxRuleGrid.addExtension(new SubmitRowActionExtension());

  new TaxRulesGroupForm();
});
