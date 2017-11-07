scenario('Create Standard Product', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to product menu', () => client.goToProductMenu());
  test('should click on the add new product button', () => client.addNewProduct());
  scenario('Edit Basic settings', client => {
    test('should set the name of product', () => client.setProductName('pack'));
    test('should set the quantity of product', () => client.setQuantity());
    test('should set the price of product', () => client.setPrice());
    test('should upload the picture one of product', () => client.uploadPicture('1.png'));
    test('should upload the picture two of product', () => client.uploadPicture('2.jpg'));
    test('should upload the picture three of product', () => client.uploadPicture('3.jpg'));
    test('should click on add category button', () => client.addCategory());
    test('should set the category name', () => client.setCategoryName('PackProduct'));
    test('should click on create category button', () => client.createCategory());
    test('should remove home category tag', () => client.removeHomeCategory());
    test('should click on add brand button', () => client.addBrand());
    test('should select brand', () => client.selectBrand());
    test('should make the product en ligne', () => client.productEnligne());
    test('should select brand', () => client.saveProduct());
    test('should close the green validation', () => client.closeGreenValidation());
  }, 'product');
}, 'product', true);
