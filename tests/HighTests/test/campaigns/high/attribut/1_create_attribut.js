scenario('Create attribut', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to attribut list', () => client.goToAttributList());
  test('should create new attribut', () => client.createAttribut());
  test('should add attribut name', () => client.addAttributName());
  test('should add attribut public name', () => client.addAttributPublicName());
  test('should add attribut type', () => client.addAttributType());
  test('should save added attribut', () => client.saveNewAttribut());
  test('should check attribut success panel', () => client.SuccessPanel('Création réussie.'));
  test('should search for the created attribut', () => client.searchAttribut());
  test('should select the created attribut', () => client.selectAttribut());
  test('should add value to the created attribut', () => client.addValueToAttribut());
  test('should check attribut success panel', () => client.SuccessPanel('Création réussie.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribut', true);


scenario('Create product', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go product menu', () => client.goToProductMenu());
  test('should add new product', () => client.addNewProduct());
  test('should add product name', () => client.addProductName());
  test('should add product quantity', () => client.addProductQuantity());
  test('should add product price', () => client.addProductPrice());
  test('should add product type', () => client.addProductType('attribut'));
  test('should make the product en ligne', () => client.productEnligne());
  test('should save the product', () => client.saveProduct());
  test('should close the green validation', () => client.closeGreenValidation());
  test('should sign out BO', () => client.signoutBO());
}, 'create_product', true);
