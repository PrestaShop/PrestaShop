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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const {AccessPageBO} = require('../../../../../selectors/BO/access_page');
const commonCurrency = require('../../../../common_scenarios/currency');
const {Localization} = require('../../../../../selectors/BO/international/localization');
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
  successMessage = '×\nSuccessful creation.',
  wrongMessage = '×\n2 errors\nThe currency conversion rate cannot be equal to 0.\nThe conversion_rate field is invalid.',
  updateSuccessMessage = '×\nSuccessful update.',
  deleteSuccessMessage = '×\nSuccessful deletion.';

scenario('Create, edit, delete and live exchange rate currency', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Test1: create, check and sort "Currency"', () => {
    commonCurrency.accessToCurrencies();
    commonCurrency.createCurrency(wrongMessage, wrongCurrencyData, false, false);
    commonCurrency.createCurrency(successMessage, firstCurrencyData, false, true, false);
    commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
    scenario('Enable currency', client => {
      test('should click on "Enable icon"', () => client.waitForExistAndClick(Localization.Currencies.check_icon.replace('%ID', 1)
        .replace('%ICON', "icon-remove")));
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
    commonCurrency.sortCurrency(Localization.Currencies.currency_iso_code_column, 'ISO code');
    commonCurrency.sortCurrency(Localization.Currencies.currency_exchange_rate, 'Exchange rate', true);
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
        .replace('%ICON', "icon-remove")));
    }, 'common_client');
    commonCurrency.editCancelCurrency();
    scenario('Verify that we go back to the currencies page and nothing has changed', client => {
      test('should check the appearance of currencies table', () => client.waitForExist(Localization.Currencies.table_currencies));
      test('should check that "Exchange rate" is equal to ' + editedCurrencyData.exchangeRate, () => client.checkTextValue(Localization.Currencies.currency_exchange_rate.replace('%ID', 1), editedCurrencyData.exchangeRate));
      test('should check that the "Status" icon is well disabled', () => client.waitForExist(Localization.Currencies.check_icon.replace('%ID', 1)
        .replace('%ICON', "icon-remove")));
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
      test('should click on "Live exchange rates" toggle button', () => client.waitForExistAndClick(Localization.Currencies.live_exchange_rate_toggle_button));
    }, 'common_client');
  }, 'common_client');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
