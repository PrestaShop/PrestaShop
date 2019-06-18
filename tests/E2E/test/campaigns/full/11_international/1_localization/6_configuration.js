/**
 * This script is based on the scenario described in this test link
 * [id="PS-142"][Name="Configuration"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {languageFO} = require('../../../../selectors/FO/index');
const commonLocalization = require('../../../common_scenarios/localization');
const commonCurrency = require('../../../common_scenarios/currency');
const {Localization} = require('../../../../selectors/BO/international/localization');
const {accountPage} = require('../../../../selectors/FO/add_account_page');
const welcomeScenarios = require('../../../common_scenarios/welcome');
const data = require('../../../../datas/country_language');
let promise = Promise.resolve();

let defaultLocalUnitsData = {
    weight: 'kg',
    distance: 'km',
    volume: 'cl',
    dimension: 'cm'
  },
  currencyData = {
    name: 'USD',
    exchangeRate: '1.13'
  };

/**
 * This script should be moved to the campaign full when this issue will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/10744
 **/
scenario('"Configuration"', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
    commonLocalization.importLocalization('United', 'United States');
    commonLocalization.importLocalization('Italia', 'Italia');
    commonLocalization.configureLocalization();
    scenario('Verify "Local units" fields', client => {
      test('should verify that the weight unit is Kg', () => client.checkAttributeValue(Localization.Localization.local_unit_input.replace('%D', 'weight'), 'value', 'kg'));
      test('should verify that the distance unit is km', () => client.checkAttributeValue(Localization.Localization.local_unit_input.replace('%D', 'distance'), 'value', 'km'));
      test('should verify that the volume unit is L', () => client.checkAttributeValue(Localization.Localization.local_unit_input.replace('%D', 'volume'), 'value', 'L'));
      test('should verify that the dimension unit is cm', () => client.checkAttributeValue(Localization.Localization.local_unit_input.replace('%D', 'dimension'), 'value', 'cm'));
      test('should click on "View my shop" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1))
      });
      test('should check that the front office language is equal to the browser language', () => {
        return promise
          .then(() => client.getAttributeInVar(languageFO.html_selector, 'lang', 'language'))
          .then(() => client.getNavigatorLanguage())
          .then((navigatorLanguage) => expect(navigatorLanguage.value).to.contain(tab['language']));
      });
      test('should verify that the selected currency is "Euro"', () => client.waitForExist(AccessPageFO.selected_currency_option.replace("%D", "EUR €")));
      test('should click on the "Sign in" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
      test('should click on "No account? Create one here" button', () => client.waitForExistAndClick(accountPage.create_button));
      test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, 'Adam'));
      test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, 'Martin'));
      test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, 'newpub' + date_time + '@prestashop.com'));
      test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, '123456'));
      test('should click on "Save" button', () => client.waitForExistAndClick(accountPage.save_account_button));
      test('should click on "Name First name" button', () => client.waitForExistAndClick(accountPage.name_firstname_link));
      test('should click on "ADD FIRST ADDRESS" button', () => client.waitForExistAndClick(accountPage.add_first_address));
      test('Verify the country selected is the same as your browser language', () => {
        return promise
          .then(() => client.waitForExist(accountPage.selected_default_country_option_list))
          .then(() => client.getText(accountPage.selected_default_country_option_list))
          .then((variable) => {
            if (tab["language"] === 'fr') {
              promise.then(() => expect(data.languages.Frensh[variable]).to.contain(tab['language']))
            } else {
              promise.then(() => expect(data.languages.English[variable]).to.contain(tab['language']))
            }
          });
      });
      test('should click on the "Sign out" button', () => client.waitForExistAndClick(AccessPageFO.sign_out_button));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'international');
    commonLocalization.configureLocalization(false, true, 'Dollar');
    scenario('Go to Front Office then verify language and currency', client => {
      test('should click on "View my shop" then go to the Front Office', () => {
        return promise
          .then(() => client.deleteCookieWithoutRefresh())
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(2));
      });

      /**
       * This error is based on the bug described in this ticket
       * https://github.com/PrestaShop/PrestaShop/issues/10744
       **/
      test('should verify that the selected language is "Italiano"', () => client.waitForExist(AccessPageFO.selected_language_option.replace("%D", "Italiano")));
      test('should verify that the selected currency is "Dollar"', () => client.waitForExist(AccessPageFO.selected_currency_option.replace("%D", "USD $")));
      test('should click on the "Accedi" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
      test('should click on "Non hai ancora un account? Creane ora qui uno" button', () => client.waitForExistAndClick(accountPage.create_button));
      test('should set the "nome" input', () => client.waitAndSetValue(accountPage.firstname_input, "Nataly"));
      test('should set the "Cognome" input', () => client.waitAndSetValue(accountPage.lastname_input, "Martin"));
      test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, 'newpub2' + date_time + '@prestashop.com'));
      test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, '123456123'));
      test('should click on "Salva" button', () => client.waitForExistAndClick(accountPage.save_account_button));
      test('should click on "Name First name" button', () => client.waitForExistAndClick(accountPage.name_firstname_link));
      test('should click on "Aggiungi il primo indrizzio" button', () => client.waitForExistAndClick(accountPage.add_first_address));
      test('should verify that the selected country is "Italy"', () => client.waitForExist(accountPage.selected_country_option_list.replace('%D', 'Italy')));
      test('should "Sign out"', () => client.signOutFO(AccessPageFO));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'international');
    commonLocalization.configureLocalization(true, false, 'Euro');
    commonLocalization.localUnits(defaultLocalUnitsData, 'cm', 'kg', 2);
    commonLocalization.deleteLanguage('Italian', false);
    scenario('Delete the "USD" currency', () => {
      commonCurrency.accessToCurrencies();
      commonCurrency.checkCurrencyByIsoCode(currencyData);
      commonCurrency.deleteCurrency(true, 'Successful deletion.');
    }, 'common_client');
    scenario('Logout from the Back Office', client => {
      test('should logout successfully from Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'common_client', true);
