const {Taxes} = require('../../../../selectors/BO/international/taxes');
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
  createTaxRule: function (taxData) {
    scenario('Create a "Tax rules"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should click on "Add new tax rules group" button', () => client.waitForExistAndClick(Taxes.taxRules.add_new_tax_rules_group_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Taxes.taxRules.name_input, taxData.name + date_time));
      test('should set the "Enable" of tax rule to "Yes"', () => client.waitForExistAndClick(Taxes.taxRules.enable_button));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_and_stay_button));
      test('should select a "Tax rule"', () => client.waitAndSelectByValue(Taxes.taxRules.tax_select, taxData.tax_value));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Taxes.taxRules.success_alert));
    }, 'common_client');
  },
  editTaxRule: function (taxData) {
    scenario('Create a "Tax rules"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, taxData.name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Taxes.taxRules.edit_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Taxes.taxRules.name_input, taxData.name + date_time + 'update'));
      test('should click on "Save and stay" button', () => client.waitForExistAndClick(Taxes.taxRules.save_and_stay_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Taxes.taxRules.success_alert));
    }, 'common_client');
  },
  deleteTaxRule: function (name) {
    scenario('Create a "Tax rules"', client => {
      test('should go to "Taxes" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu));
      test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
      test('should search for the created tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, name));
      test('should delete the tax rules', () => {
        return promise
          .then(() => client.waitForExistAndClick(Taxes.taxRules.dropdown_button))
          .then(() => client.waitForExistAndClick(Taxes.taxRules.delete_button))
          .then(() => client.alertAccept())
      });
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Taxes.taxRules.success_alert));
    }, 'common_client');
  }
};