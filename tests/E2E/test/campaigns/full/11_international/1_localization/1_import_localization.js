/**
 * This script is based on the scenario described in this test link
 * [id="PS-141"][Name="Import a localization pack and check you can use it"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const commonLocalization = require('../../../common_scenarios/localization');
const commonCurrency = require('../../../common_scenarios/currency');
const {Menu} = require('../../../../selectors/BO/menu.js');
const {Taxes} = require('../../../../selectors/BO/international/taxes');
const {Location} = require('../../../../selectors/BO/international/location');
const {Localization} = require('../../../../selectors/BO/international/localization');
const welcomeScenarios = require('../../../common_scenarios/welcome');

const firstCurrencyData = {
    name: 'AED',
    exchangeRate: '0,86'
  },
  secondCurrencyData = {
    name: 'USD',
    exchangeRate: '0,86'
  },
  thirdCurrencyData = {
    name: 'EUR',
    exchangeRate: '0,86'
  };
let firstLocalUnitsData = {
  weight: 'kg',
  distance: 'km',
  volume: 'cl',
  dimension: 'cm'
};

scenario('"Import a localization pack and check you can use it"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonLocalization.getDefaultConfiguration();
  commonLocalization.importLocalization('United', 'United Arab');
  commonLocalization.checkUnchangedDefaultConfiguration();
  commonLocalization.checkExistenceLanguage('Arabic');
  commonCurrency.accessToCurrencies();
  commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
  commonLocalization.getDefaultConfiguration();
  commonLocalization.importLocalization('United', 'United States', true);
  commonLocalization.checkUnchangedDefaultConfiguration();
  commonLocalization.checkExistenceLanguage('English');
  commonCurrency.accessToCurrencies();
  commonCurrency.checkCurrencyByIsoCode(secondCurrencyData);
  scenario('Go to "Taxes" page then check existence taxes', client => {
    test('should go to "Taxes" page', async () => {
      await client.waitForExistAndClick(Menu.Improve.International.international_menu);
      await client.waitForExistAndClick(Menu.Improve.International.taxes_submenu);
    });
    test('should search for "Sales-taxes US-XX X%" tax', () => client.searchByValue(Taxes.taxes.filter_name_input, Taxes.taxes.filter_search_button, 'Sales-taxes US'));
    test('should check  "Sales-taxes US-XX X%" exists', () => client.checkTextValue(Taxes.taxes.tax_field_column.replace('%L', 1).replace('%C', 3), 'Sales-taxes US', 'contain'));
    test('should click on "Tax Rules" tab', () => client.waitForExistAndClick(Menu.Improve.International.taxe_rules_tab));
    test('should search for "US-XX Rate (X%)" tax rule', () => client.searchByValue(Taxes.taxRules.filter_name_input, Taxes.taxRules.filter_search_button, 'US'));
    test('should check  "US-XX Rate (X%)" exists', () => client.checkTextValue(Taxes.taxRules.tax_field_column.replace('%L', 1).replace('%C', 3), 'US', 'contain'));
  }, 'common_client');
  scenario('Go to "Locations" page then check existence taxes', client => {
    test('should go to "Locations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu));
    test('should click on "States" tab', () => client.waitForExistAndClick(Menu.Improve.International.states_tab));
    test('should select "United States" in country column', () => client.waitAndSelectByVisibleText(Location.State.country_select, 'United States'));
    test('should click on "Search" button', () => client.waitForExistAndClick(Location.State.search_button));
    test('should check  "United States" exists', () => client.checkTextValue(Location.State.state_field_column.replace('%L', 1).replace('%C', 6), 'United States', 'contain'));
  }, 'common_client');
  commonLocalization.getDefaultConfiguration();
  commonLocalization.importLocalization('Ita', 'Italia', 1, true, true);
  commonLocalization.checkUnchangedDefaultConfiguration();
  commonLocalization.checkExistenceLanguage('Italian');
  commonCurrency.accessToCurrencies();
  commonCurrency.checkCurrencyByIsoCode(thirdCurrencyData);
  scenario('Delete created languages and currencies and edit default local units', () => {
    commonLocalization.deleteLanguage('Arabic', false);
    commonLocalization.deleteLanguage('Italian', false);
    commonCurrency.accessToCurrencies();
    commonCurrency.checkCurrencyByIsoCode(firstCurrencyData);
    commonCurrency.deleteCurrency(true, 'Successful deletion.');
    commonCurrency.checkCurrencyByIsoCode(secondCurrencyData);
    commonCurrency.deleteCurrency(true, 'Successful deletion.');
    scenario('Click on "Reset" button', client => {
      test('should click on reset button', () => client.waitForExistAndClick(Localization.Currencies.reset_button));
    }, 'common_client');
    commonLocalization.localUnits(firstLocalUnitsData, 'cm', 'kg', 2, false);
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
