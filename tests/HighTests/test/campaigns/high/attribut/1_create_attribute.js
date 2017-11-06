scenario('Create attribute', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('Should go to the attribute list', () => client.goToAttributeList());
  test('should create new attribute', () => client.createAttribute());
  test('should add attribute name', () => client.addAttributeName());
  test('should add attribute public name', () => client.addAttributePublicName());
  test('should add attribute type', () => client.addAttributeType());
  test('should save added attribute', () => client.saveNewAttribute());
  test('should check attribute success panel', () => client.successPanel('Successful creation.'));
  test('should search for the created attribute', () => client.searchAttribute());
  test('should select the created attribute', () => client.selectAttribute());
  test('should add value to the created attribute', () => client.addValueToAttribute());
  test('should check attribute success panel', () => client.successPanel('Successful creation.'));
  test('should sign out BO', () => client.signoutBO());
}, 'attribute', true);

scenario('Create product', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go product menu', () => client.goToProductMenu());
  test('should add new product', () => client.addNewProduct());
  test('should add product name', () => client.addProductName());
  test('should add product quantity', () => client.addProductQuantity());
  test('should add product price', () => client.addProductPrice());
  test('should add product type', () => client.addProductTypeAttribute());
  test('should make the product en ligne', () => client.productEnligne());
  test('should save the product', () => client.saveProduct());
  test('should close the green validation', () => client.closeGreenValidation());
  test('should sign out BO', () => client.signoutBO());
}, 'create_product', true);

scenario('Check the product attribute in the Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should check the product attribute name', () => client.checkCreatedAttributeName());
  test('should Check attribute value', () => client.checkCreatedAttributeValue());
  test('should sign out FO', () => client.signoutFO());
}, 'attribute', true);
