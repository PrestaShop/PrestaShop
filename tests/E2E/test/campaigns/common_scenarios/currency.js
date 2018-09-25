const {Menu} = require('../../selectors/BO/menu.js');
const {Localization} = require('../../selectors/BO/international/localization');

/**** Example of currency data ****
 * let currencyData = {
 *  name: 'currency name',
 *  exchangeRate: 'exchange rate of currency',
 * };
 */

module.exports = {
  accessToCurrencies() {
    scenario('Access to "Currencies" page', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Currencies" subtab', () => client.waitForExistAndClick(Menu.Improve.International.currencies_tab));
    }, 'common_client');
  },

  checkCurrencyByIsoCode(currencyData) {
    scenario('Search then check the "' + (currencyData.name).toUpperCase() + '" currency in the back office', client => {
      test('should search for the currency by "Iso code"', () => client.searchByValue(Localization.Currencies.search_iso_code_input, Localization.Currencies.search_button, currencyData.name));
      test('should check the appearance of the  currency', () => client.checkTextValue(Localization.Currencies.currency_iso_code_column.replace('%ID', 1), currencyData.name, 'contain'));
    }, 'common_client');
  },

  deleteCurrency(confirmDelete, message = '') {
    scenario('Delete "Currency"', client => {
      test('should click on "Delete" action from dropdown list', () => client.clickOnAction(Localization.Currencies.delete_action_button, Localization.Currencies.action_button, 'delete', confirmDelete));
      if (message !== '') {
        test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'success'), message));
      }
    }, 'currency');
  },
};
