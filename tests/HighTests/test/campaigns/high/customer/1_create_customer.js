scenario('Create customer', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to customer menu', () => client.goToCustomersMenu());
  test('should click on add new customer button', () => client.addNewCustomer());
  test('should add social title', () => client.addSocialTitle());
  test('should add first name', () => client.addFirstName());
  test('should add last name', () => client.addLastName());
  test('should add email address', () => client.addEmailAddress());
  test('should add password', () => client.addPassword());
  test('should add birthday', () => client.addBirthday());
  test('should save customer', () => client.saveCustomer());
  test('should check that the success alert message is well displayed', () => client.successPanel('Successful creation.'));
  test('should sign out BO', () => client.signoutBO());
}, 'customer', true);
