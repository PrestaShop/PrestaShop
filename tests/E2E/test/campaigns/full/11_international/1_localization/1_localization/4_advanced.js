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
const {Menu} = require('../../../../../selectors/BO/menu.js');
const commonLocalization = require('../../../../common_scenarios/localization');

let firstAdvancedData = {
    languageIdentifier: 'fr',
    countryIdentifier: 'fr',
  },
  secondAdvancedData = {
    languageIdentifier: 'en',
    countryIdentifier: 'gb',
  },
  thirdAdvancedData = {
    languageIdentifier: 'en',
    countryIdentifier: 'fr',
  };

scenario('"Advanced"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Access to "International > Localization" page', client => {
    test('should go to International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
  }, 'common_client');
  commonLocalization.updateAdvancedData(firstAdvancedData);
  commonLocalization.updateAdvancedData(secondAdvancedData);
  commonLocalization.updateAdvancedData(thirdAdvancedData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
