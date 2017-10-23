scenario('Create attribut', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to attribut list', () => client.goToAttributList());
  test('should search for the created attribut', () => client.searchAttribut());
  test('should select the created attribut', () => client.selectAttribut());
  test('should add value to the created attribut', () => client.addValueToAttribut());

  test('should check attribut success panel', () => client.SuccessPanel('Création réussie.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribut', true);


scenario('The Check of the Product attribute in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check the Product attribute in Front Office', () => client.checkForProductAttributFO('create'));

}, 'attribut', true);
