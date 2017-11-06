scenario('Delete attribute', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('Should go to the attribute list', () => client.goToAttributeList());
  test('should search for the updated attribute', () => client.searchAttribute());
  test('should delete attribute', () => client.deleteAttribute());
  test('should check attribute success panel', () => client.successPanel('Successful deletion.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribute', true);

scenario('Check of the deleted attribute in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check deleted attribute in Front Office', () => client.checkDeletedAttributeFO());
  test('should sign out FO', () => client.signoutFO());
}, 'attribute', true);
