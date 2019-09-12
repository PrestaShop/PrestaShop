const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {Menu} = require('../../selectors/BO/menu.js');
const {Localization} = require('../../selectors/BO/international/localization');
let promise = Promise.resolve();
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
      test('should click on "Currencies" subtab', () => {
        return promise
          .then(() => client.waitForExistAndClick(Menu.Improve.International.currencies_tab))
          .then(() => client.waitForVisible(Menu.Improve.International.active_currencies_tab));
      });
    }, 'common_client');
  },

  createCurrency(message, currencyData, statusCurrency = false, successMessage = true, addButton = true) {
    scenario('Create a new "Currency"', client => {
      if (addButton) {
        test('should click on "Add new currency" button', () => client.waitForExistAndClick(Localization.Currencies.new_currency_button));
      }
      test('should choose the "' + currencyData.name + '" currency from the list', () => client.waitAndSelectByValue(Localization.Currencies.currency_select, currencyData.name));
      test('should set the "Exchange rate" input', () => client.waitAndSetValue(Localization.Currencies.exchange_rate_input, currencyData.exchangeRate));
      if (statusCurrency) {
        test('should click on "Status" toggle button', () => client.waitForExistAndClick(Localization.Currencies.status_currency_toggle_button.replace('%ID', 1)));
      }
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Currencies.save_button));
      if (successMessage) {
        test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'success'), message));
      } else {
        test('should verify the appearance of the red error', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'danger'), message));
      }
    }, 'common_client');
  },

  checkCurrencyByIsoCode(currencyData) {
    scenario('Search then check the "' + (currencyData.name).toUpperCase() + '" currency in the back office', client => {
      test('should search for the currency by "Iso code"', () => client.searchByValue(Localization.Currencies.search_iso_code_input, Localization.Currencies.search_button, currencyData.name));
      test('should check the appearance of the  currency', () => client.checkTextValue(Localization.Currencies.currency_iso_code_column.replace('%ID', 1), currencyData.name, 'contain'));
    }, 'common_client');
  },

  sortCurrency: async function (selector, sortBy, isNumber = false) {
    global.elementsSortedTable = [];
    global.elementsTable = [];
    scenario('Check the sort of currencies by "' + sortBy.toUpperCase() + '"', client => {
      test('should get the number of currencies', async () => {
        await client.pause(2000);
        await client.getCurrencyNumber(Localization.Currencies.currency_number_span, 'number_currency');
      });
      test('should click on "Sort by ASC" icon', async () => {
        for (let j = 0; j < (parseInt(tab['number_currency'])); j++) {
          await client.getTableField(selector, j);
        }
        await client.moveToObject(Localization.Currencies.sort_icon.replace("%B", sortBy));
        await client.waitForExistAndClick(Localization.Currencies.sort_icon.replace("%B", sortBy));
      });
      test('should check that the currencies are well sorted by ASC', async () => {
        for (let j = 0; j < (parseInt(tab['number_currency'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber);
      });
      test('should click on "Sort by DESC" icon', async () => {
        await client.moveToObject(Localization.Currencies.sort_icon.replace("%B", sortBy));
        await client.waitForExistAndClick(Localization.Currencies.sort_icon.replace("%B", sortBy));
      });
      test('should check that the currencies are well sorted by DESC', async () => {
        for (let j = 0; j < (parseInt(tab['number_currency'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber, 'DESC');
      });
    }, 'currency');
  },

  checkCurrencyByStatus() {
    scenario('Search the currency in the back office by "Status"', client => {
      test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
      test('should choose "Yes" from enabled filter list', async () => {
        await client.pause(2000);
        await client.waitAndSelectByValue(Localization.Currencies.enabled_select, '1')
      });
      test('should click on Search button', () => client.waitForExistAndClick(Localization.Currencies.search_button));
      test('should get the number of currencies', async () => await client.getCurrencyNumber(Localization.Currencies.currency_number_span, 'number_currency'));
      test('should check if all the displayed currencies are enabled ', () => {
        for (let j = 1; j <= (parseInt(tab['number_currency'])); j++) {
          promise = client.waitForExist(Localization.Currencies.check_icon.replace('%ID', j).replace('%ICON', 'icon-valid'));
        }
        return promise
      });
      test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
      test('should choose "No" from enabled filter list', async () => {
        await client.pause(2000);
        await client.waitAndSelectByValue(Localization.Currencies.enabled_select, '0')
      });
      test('should click on Search button', () => client.waitForExistAndClick(Localization.Currencies.search_button));
      test('should check that currencies are not existing', () => client.checkTextValue(Localization.Currencies.search_no_results, 'No records found'));
      test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
    }, 'currency');
  },

  editCurrency(editedCurrencyData, message, changeRate = true, statusCurrency = true) {
    scenario('Edit "Currency"', client => {
      test('should click on "Edit" action from the dropdown list', () => client.waitForExistAndClick(Localization.Currencies.edit_button));
      if (changeRate) {
        test('should set the "Exchange rate" input', () => client.waitAndSetValue(Localization.Currencies.exchange_rate_input, editedCurrencyData.exchangeRate));
      }
      if (statusCurrency) {
        test('should disable the edited currency', () => client.waitForExistAndClick(Localization.Currencies.status_currency_toggle_button.replace('%ID', 0)));
      }
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Currencies.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'success'), message));
    }, 'currency');
  },

  editCancelCurrency() {
    scenario('Edit and cancel "Currency"', client => {
      test('should click on "Edit" action from the dropdown list', () => client.clickOnAction(Localization.Currencies.edit_button));
      test('should click on "Cancel" button', () => client.waitForExistAndClick(Localization.Currencies.cancel_button));
    }, 'currency');
  },

  deleteCurrency(confirmDelete, message = '') {
    scenario('Delete "Currency"', client => {
      test('should click on "Delete" action from dropdown list', () => client.clickOnAction(Localization.Currencies.delete_action_button, Localization.Currencies.action_button, 'delete', confirmDelete));
      if (message !== '') {
        test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'success'), message));
      }
    }, 'currency');
  },

  checkCurrencyFO(currencyData, exist = "exist") {
    scenario('Check that the currency ' + exist + ' in the Front Office', client => {
      test('should click on "Shop name" then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1))
          .then(() => client.changeLanguage());

      });
      test('should open currencies list', () => client.waitForExistAndClick(AccessPageFO.currency_list_select));
      if (exist === "exist") {
        test('should check the existence of the created currency', () => client.waitForVisible(AccessPageFO.currency_list_element.replace('%NAME', currencyData.name)));
      }
      else {
        test('should check the not existence of the currency', () => client.isNotExisting(AccessPageFO.currency_list_element.replace('%NAME', currencyData.name)));
      }
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'currency');
  },

  liveExchangeRate() {
    scenario('Live exchange rate', client => {
      test('should click on "Live exchange rates" toggle button', () => client.waitForExistAndClick(Localization.Currencies.live_exchange_rate_toggle_button.replace('%ID', 1)));
      test('should click on update exchange rates', () => client.waitForExistAndClick(Localization.Currencies.update_exchange_rate_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Localization.Currencies.success_danger_panel.replace('%B', 'success'), 'Successful update.'));
    }, 'common_client');
  },

};
