const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const commonScenarios = require('../../../common_scenarios/taxes');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let taxData = {
  name: 'VAT',
  tax_value: '19'
};

scenario('Create, edit, delete and check "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonScenarios.createTaxRule(taxData.name, taxData.tax_value);
  commonScenarios.checkTaxRule(taxData.name);
  commonScenarios.editTaxRule(taxData.name, taxData.name + 'update');
  commonScenarios.checkTaxRule(taxData.name + 'update');
  commonScenarios.deleteTaxRule(taxData.name + 'update');
  commonScenarios.createTaxRule(taxData.name, taxData.tax_value);
  commonScenarios.deleteTaxRuleWithBulkAction(taxData.name);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
