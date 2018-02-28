const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./taxes');

let taxData = {
  name: 'VAT',
  tax_value: '19'
};

scenario('Create, edit, delete and check "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createTaxRule(taxData.name + date_time, taxData.tax_value);
  common_scenarios.checkTaxRule(taxData.name + date_time);
  common_scenarios.editTaxRule(taxData.name + date_time, taxData.name + date_time + 'update');
  common_scenarios.checkTaxRule(taxData.name + date_time + 'update');
  common_scenarios.deleteTaxRule(taxData.name + date_time + 'update');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
