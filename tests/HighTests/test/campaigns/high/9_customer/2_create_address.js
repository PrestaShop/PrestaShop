const {AccessPageBO} = require('../../../selectors/BO/access_page');

scenario('Create Address', client => {
  test('should open the browser', () => client.open());
  test('should sign in the Back Office', () => client.signInBO(AccessPageBO));
  test('should go to Address menu', () => client.goToCustomersAddress());
  test('should click on add new address', () => client.newAddress());
  test('should add customer email', () => client.addCustomerEmail());
  test('should add identification number', () => client.addIdNumber());
  test('should add address alias', () => client.addAliasAddress());
  test('should add first name', () => client.addFirstName());
  test('should add last name', () => client.addLastName());
  test('should add company', () => client.addCompanyName());
  test('should add VAT number', () => client.addTVANumber());
  test('should add address', () => client.addAddress());
  test('should add second address', () => client.addSecondAddress());
  test('should add postal code', () => client.addZIPCode());
  test('should add city', () => client.addCity());
  test('should add pays', () => client.addCountry());
  test('should add home phone', () => client.addHomePhone());
  test('should add other information', () => client.addOther());
  test('should save the new address', () => client.saveAddress());
  test('should check that the success alert message is well displayed', () => client.successPanel('Successful creation.'));
  test('should sign Out the Back Office', () => client.signOutBO());
}, 'customer', true);
