/**
 * 2007-2019 PrestaShop SA and Contributors
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

/**
 * Defines all selectors that are used in tax rules group add/edit form.
 */
export default {
  countrySelect: '#tax_rule_country_id',
  stateFormRow: '.js-state-form-row',
  stateFormSelect: '#tax_rule_state_ids',
  editTaxRuleLink: 'a.js-link-row-action',
  addTaxRuleBtn: '#page-header-desc-configuration-add',
  submitTaxRuleBtn: '#tax-rule-submit-btn',
  taxRuleForm: '#tax-rule-form',
  zipCodeInput: '#tax_rule_zip_code',
  behaviorSelect: '#tax_rule_behavior_id',
  taxSelect: '#tax_rule_tax_id',
  descriptionInput: '#tax_rule_description',
  spinnerContainer: '#spinner-container',
  addLink: '#page-header-desc-configuration-add',
  taxRulesHiddenContent: '.js-hidden-content',
  taxRuleErrorPopoverContent: '.js-popover-error-content',
  taxRuleInvalidFeedbackContainer: '.invalid-feedback-container',
  taxRuleFormPopoverErrorContainers: [
    '.popover-error-container-tax_rule_description',
    '.popover-error-container-tax_rule_tax_id',
    '.popover-error-container-tax_rule_behavior_id',
    '.popover-error-container-tax_rule_state_ids',
    '.popover-error-container-tax_rule_country_id',
  ],
  taxRuleFormErrorAlert: '.alert.alert-danger',
  taxRulesGrid: 'form[name="tax_rule_grid"]',
}
