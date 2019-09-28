/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-148"][Name="Add a currency"]
 * [id="PS-149"][Name="edit a currency"]
 * [id="PS-150"][Name="Delete a currency"]
 * [id="PS-151"][Name="Live exchange rates"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const commonCurrency = require('../../../common_scenarios/currency');
const {Localization} = require('../../../../selectors/BO/international/localization');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let wrongCurrencyData = {
    name: 'CHF',
    exchangeRate: '0,86'
  },
  firstCurrencyData = {
    name: 'CHF',
    exchangeRate: '0.86'
  },
  secondCurrencyData = {
    name: 'DZD',
    exchangeRate: '0.00730'
  },
  currencyData = {
    name: 'CHF',
    exchangeRate: '0.86'
  },
  editedCurrencyData = {
    name: 'CHF',
    exchangeRate: '1.86'
  },
  successMessage = 'Successful creation.',
  wrongMessage = '×\n2 errors\nThe currency conversion rate cannot be equal to 0.\nThe conversion_rate field is invalid.',
  updateSuccessMessage = 'Successful update.',
  deleteSuccessMessage = 'Successful deletion.';

scenario('Create, edit, delete and live exchange rate currency', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Test1: create, check and sort "Currency"', () => {
    commonCurrency.accessToCurrencies();
    /**
     * Behavior changed
     * In Add currency form,, '0,86' was not accepted
     * Now, '0,86' and '0.86' are both accepted
     * commonCurrency.createCurrency(wrongMessage, wrongCurrencyData, false, false);
     */
    commonCurrency.createCurrency(successMessage, firstCurrencyData, false, true);
    commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
    scenario('Enable currency', client => {
      test('should click on "Enable icon"', () => client.waitForExistAndClick(Localization.Currencies.check_icon.replace('%ID', 1)
        .replace('%ICON', "not-valid")));
    }, 'common_client');
    commonCurrency.checkCurrencyFO(firstCurrencyData);
    commonCurrency.accessToCurrencies();
    commonCurrency.createCurrency(successMessage, secondCurrencyData, true);
    commonCurrency.checkCurrencyByIsoCode(secondCurrencyData);
    commonCurrency.checkCurrencyFO(secondCurrencyData);
    commonCurrency.accessToCurrencies();
    scenario('Click on "Reset" button', client => {
      test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
    }, 'common_client');
    commonCurrency.sortCurrency(Localization.Currencies.currency_iso_code_column, 'iso_code');
    commonCurrency.sortCurrency(Localization.Currencies.currency_exchange_rate, 'conversion_rate', true);
    commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
    commonCurrency.checkCurrencyByStatus();
  }, 'common_client');

  scenario('Test2: edit "Currency"', () => {
    commonCurrency.accessToCurrencies();
    commonCurrency.checkCurrencyByIsoCode(currencyData);
    commonCurrency.editCurrency(editedCurrencyData, updateSuccessMessage, true, false);
    scenario('Check the "Exchange rate" of the edited currency', client => {
      test('should check that "Exchange rate" is equal to ' + editedCurrencyData.exchangeRate, () => client.checkTextValue(Localization.Currencies.currency_exchange_rate.replace('%ID', 1), editedCurrencyData.exchangeRate));
    }, 'common_client');
    commonCurrency.accessToCurrencies();
    commonCurrency.checkCurrencyByIsoCode(currencyData);
    commonCurrency.editCurrency(editedCurrencyData, updateSuccessMessage, false);
    scenario('Check the "Status" of the edited currency', client => {
      test('should check that the "Status" icon is well disabled', () => client.waitForExist(Localization.Currencies.check_icon.replace('%ID', 1)
        .replace('%ICON', "not-valid")));
    }, 'common_client');
    commonCurrency.editCancelCurrency();
    scenario('Verify that we go back to the currencies page and nothing has changed', client => {
      test('should check the appearance of currencies table', () => client.waitForExist(Localization.Currencies.table_currencies));
      test('should check that "Exchange rate" is equal to ' + editedCurrencyData.exchangeRate, () => client.checkTextValue(Localization.Currencies.currency_exchange_rate.replace('%ID', 1), editedCurrencyData.exchangeRate));
      test('should check that the "Status" icon is well disabled', () => client.waitForExist(Localization.Currencies.check_icon.replace('%ID', 1)
        .replace('%ICON', "not-valid")));
    }, 'common_client');
  }, 'common_client');

  scenario('Test3: delete "Currency"', () => {
    commonCurrency.accessToCurrencies();
    commonCurrency.checkCurrencyByIsoCode(currencyData);
    commonCurrency.deleteCurrency(false);
    commonCurrency.checkCurrencyByIsoCode(currencyData);
    commonCurrency.deleteCurrency(true, deleteSuccessMessage);
    scenario('Check that the currency is well deleted', client => {
      test('should check that currency does not exist', () => client.checkTextValue(Localization.Currencies.search_no_results, 'No records found', 'contain'));
    }, 'common_client');
    commonCurrency.checkCurrencyFO(currencyData, 'does not exist');
  }, 'common_client');

  scenario('Test4: Live exchange "Currency"', () => {
    commonCurrency.accessToCurrencies();
    commonCurrency.liveExchangeRate();
  }, 'common_client');

  scenario('Delete the "DZD" currency then click on "Live exchange rates" toggle button', () => {
    commonCurrency.checkCurrencyByIsoCode(secondCurrencyData);
    commonCurrency.deleteCurrency(true, deleteSuccessMessage);
    scenario('Turn the "Live exchange rates" toggle button disable', client => {
      test('should click on "Live exchange rates" toggle button', () => client.waitForExistAndClick(Localization.Currencies.live_exchange_rate_toggle_button.replace('%ID', 0)));
    }, 'common_client');
  }, 'common_client');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
