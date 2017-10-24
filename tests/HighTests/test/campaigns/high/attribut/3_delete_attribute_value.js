scenario('Delete attribute value', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('Should go to the attribute list', () => client.goToAttributeList());
  test('should search for the updated attribute', () => client.searchAttribute());
  test('should select the attribute', () => client.selectAttribute());
  test('should delete the attribute value', () => client.deleteAttributeValue());
  test('should check attribute success panel', () => client.SuccessPanel('Successful deletion.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribute', true);

scenario('Check the product deleted attribute value in the Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check updated attribute in Front Office', () => client.checkdeletedAttributeValue());
  test('should sign out FO', () => client.signoutFO());
}, 'attribute', true);
