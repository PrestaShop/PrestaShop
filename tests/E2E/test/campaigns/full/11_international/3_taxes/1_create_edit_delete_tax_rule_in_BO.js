const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./taxes');

var taxData = {
  name: 'VAT',
  tax_value: '19'
};

scenario('Create "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createTaxRule(taxData);
  scenario('Save change', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Edit "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.editTaxRule(taxData);
  scenario('Save change', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Delete "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.deleteTaxRule(taxData.name + date_time + 'update');
  scenario('Save change', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
