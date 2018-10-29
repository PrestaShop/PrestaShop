const {AccessPageBO} = require('../../../../../selectors/BO/access_page');
const commonLocalization = require('../../../../common_scenarios/localization');
const {OnBoarding} = require('../../../../../selectors/BO/onboarding');
const {AddProductPage} = require('../../../../../selectors/BO/add_product_page');
let firstLocalUnitsData = {
    weight: 'kg',
    distance: 'km',
    volume: 'cl',
    dimension: 'cm'
  },
  secondLocalUnitsData = {
    weight: 'lb',
    distance: 'mi',
    volume: 'L',
    dimension: 'in'
  };
let promise = Promise.resolve();

scenario('"Local units"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonLocalization.localUnits(firstLocalUnitsData, 'cm', 'kg');
  scenario('Close symfony toolbar then click on "Stop the OnBoarding" button', client => {
    test('should close symfony toolbar', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.symfony_toolbar, 3000))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        });
    });
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button));
    });
  }, 'onboarding');
  commonLocalization.localUnits(secondLocalUnitsData, 'in', 'lb');
  scenario('Click on "Stop the OnBoarding" button', client => {
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button));
    });
  }, 'onboarding');
  commonLocalization.localUnits(firstLocalUnitsData, 'cm', 'kg', 2);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
