scenario('Delete attribut', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to attribut list', () => client.goToAttributList());
  test('should search for the updated attribut', () => client.searchAttribut());
  test('should delete attribut', () => client.deleteAttribut());
  test('should check attribut success panel', () => client.SuccessPanel('Suppression réussie.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribut', true);

scenario('The Check of the deleted attribute in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check deleted attribute in Front Office', () => client.checkDeletedAttributFO());
}, 'attribut', true);
