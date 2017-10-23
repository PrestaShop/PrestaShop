scenario('Delete attribut value', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to attribut list', () => client.goToAttributList());
  test('should search for the updated attribut', () => client.searchAttribut());
  test('should select the attribut', () => client.selectAttribut());
  test('should delete the attribut value', () => client.deleteAttributValue());
  test('should check attribut success panel', () => client.SuccessPanel('Suppression réussie.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribut', true);

scenario('The Check of the attribute value in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct('update'));
  test('should Check updated attribute in Front Office', () => client.checkForProductAttributFO('deleted'));
}, 'attribut', true);
