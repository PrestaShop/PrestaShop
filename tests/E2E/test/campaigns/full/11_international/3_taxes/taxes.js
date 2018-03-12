const {Taxes} = require('../../../../selectors/BO/international/taxes');
const {InternationalPage} = require('../../../../selectors/BO/international/index');
const {Menu} = require('../../../../selectors/BO/menu.js');
let promise = Promise.resolve();

module.exports = {
  /**
   * Exemple of tax rule data
   * var taxData = {
   *  name: 'name_tax_rule',
   *  tax_value: 'tax_value',
   * }
   */
  createTaxRule: function (name, value) {
    scenario('Create a new "Tax rule"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should click on "Add new tax rules group" button', () => client.waitForExistAndClick(Taxes.taxRules.add_new_tax_rules_group_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Taxes.taxRules.name_input, name + date_time));
      test('should enable the tax rule', () => client.waitForExistAndClick(Taxes.taxRules.enable_button));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_and_stay_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful creation.'));
      test('should select the "VAT IE 23%" from the tax dropdown list', () => client.waitAndSelectByValue(Taxes.taxRules.tax_select, value));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  editTaxRule: function (name, updatedName) {
    scenario('Edit the "Tax rule"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Taxes.taxRules.edit_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Taxes.taxRules.name_input, updatedName + date_time));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_and_stay_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkTaxRule: function (name) {
    scenario('Check the "Tax rule"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Taxes.taxRules.edit_button));
      test('should check that the "Name" is equal to "' + (name + date_time) + '"', () => client.checkAttributeValue(Taxes.taxRules.name_input, 'value', name + date_time));
    }, 'common_client');
  },
  deleteTaxRule: function (name) {
    scenario('Delete the "Tax rule"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, name + date_time));
      test('should click on "Dropdown toggle > Delete" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Taxes.taxRules.dropdown_button))
          .then(() => client.waitForExistAndClick(Taxes.taxRules.delete_button))
          .then(() => client.alertAccept())
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful deletion.'));
    }, 'common_client');
  },
  deleteTaxRuleWithBulkAction: function (name) {
    scenario('Delete the "Tax rule" with bulk action', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, name + date_time));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Taxes.taxRules.bulk_action_button));
      test('should click on "Select all" action', () => client.waitForExistAndClick(Taxes.taxRules.action_group_button.replace('%ID', 1)));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Taxes.taxRules.bulk_action_button));
      test('should click on "Delete" action', () => {
        return promise
          .then(() => client.waitForExistAndClick(Taxes.taxRules.action_group_button.replace('%ID', 5)))
          .then(() => client.alertAccept())
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'common_client');
  }
};