scenario('Update attribute', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('Should go to the attribute list', () => client.goToAttributeList());
  test('should search for the created attribute', () => client.searchAttribute());
  test('should update attribute name', () => client.updateAttributeName());
  test('should update attribute value', () => client.updateAttributeValue());
  test('should check attribute success panel', () => client.successPanel('Successful update.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribute', true);


scenario('Check the product attribute in the Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should change front office language to english', () => client.languageChange('english'));
  test('should search for the product', () => client.searchForProduct('Attribut'));
  test('should check the product attribute name', () => client.checkUpdatedAttributeName());
  test('should Check updated attribute in Front Office', () => client.checkUpdatedAttributeValue());
  test('should sign out FO', () => client.signoutFO());
}, 'attribute', true);


