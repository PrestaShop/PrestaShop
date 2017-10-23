scenario('Update attribut', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to attribut list', () => client.goToAttributList());
  test('should search for the created attribut', () => client.searchAttribut());
  test('should update attribut name', () => client.updateAttributName());
  test('should update attribut value', () => client.updateAttributValue());
  test('should check attribut success panel', () => client.SuccessPanel('Mise à jour réussie.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribut', true);


scenario('The Check of the Product attribute in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check updated attribute in Front Office', () => client.checkForProductAttributFO());
}, 'attribut', true);


