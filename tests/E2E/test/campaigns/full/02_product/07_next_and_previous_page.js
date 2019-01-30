/**
 * This script is based on the scenario described in this test link
 * [id="PS-374"][Name="Go to next & previous page"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const commonProductScenarios = require('../../common_scenarios/product');
const {TrafficAndSeo} = require('../../../selectors/BO/shopParameters/shop_parameters');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
let promise = Promise.resolve();
scenario('Go to next & previous page', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button));
    });
  }, 'onboarding');

  scenario('Check product pagination in the Front Office', client => {
    commonProductScenarios.checkProductPaginationFO(client, productPage, 'enable', 1);
    commonProductScenarios.checkProductPaginationFO(client, productPage, 'disable', 0, 2);
    test('should enable the "Friendly URL"', () => client.waitForExistAndClick(TrafficAndSeo.SeoAndUrls.friendly_url_button.replace('%s', 1)));
    test('should click on "Save" button', () => client.scrollWaitForExistAndClick(TrafficAndSeo.SeoAndUrls.save_button, 1000));
  }, 'product/product');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'product/product', true);
