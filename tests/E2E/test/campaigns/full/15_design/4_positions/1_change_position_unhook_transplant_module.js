/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-127"][Name="Change position"]
 * [id="PS-128"][Name="Unhook a module"]
 * [id="PS-129"][Name="Transplant a module"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../common_scenarios/positions');
const {Positions} = require('../../../../selectors/BO/design/positions');
const welcomeScenarios = require('../../../common_scenarios/welcome');
const promise = Promise.resolve();
scenario('Change Position, unhook and transplant  a module', () => {
  scenario('Test 1: Change Position of hooks', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    welcomeScenarios.findAndCloseWelcomeModal();
    common_scenarios.searchHook('displayfooter');
    common_scenarios.changePositionDragDrop();
    common_scenarios.searchHook('displayfooter');
    common_scenarios.changePositionArrow();
    scenario('logout successfully from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'common_client', true);
  scenario('Test 2: Unhook a module', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.transplantModule('Category tree links', 'displayFooter (This hook displays new blocks in the footer)', 'displayFooter');
    common_scenarios.unhookModule('Category tree links', 'displayFooter');
    scenario('Check footer in Front Office', client => {
      test('should click on "View my shop" button then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should click on the first category', () => client.waitForExistAndClick(Positions.first_category_element));
      test('should verify if the module category tree link is not in the footer', () => client.isNotExisting(Positions.category_footer_list));
    }, 'common_client');
    scenario('logout successfully from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'common_client', true);
  scenario('Test 3: Transplant a module', () => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.transplantModule('Category tree links', 'displayFooter (This hook displays new blocks in the footer)', 'displayFooter');
    scenario('Check footer in Front Office then go back to the Back Office', client => {
      test('should click on "View my shop" button then go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should click on the first category', () => client.waitForExistAndClick(Positions.first_category_element));
      test('should verify if the module category tree link is in the footer', () => client.isExisting(Positions.category_footer_list));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
    common_scenarios.unhookModule('Category tree links', 'displayFooter');
    scenario('logout successfully from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'common_client', true);
}, 'common_client');
