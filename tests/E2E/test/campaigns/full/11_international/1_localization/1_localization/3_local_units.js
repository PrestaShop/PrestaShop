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
            client.waitForExistAndClick(AddProductPage.symfony_toolbar)
          }
        });
    });
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button))
        .then(() => client.pause(2000));
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
