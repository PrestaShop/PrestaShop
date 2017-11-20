scenario('Check category radio button', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to product menu', () => client.goToProductMenu());
  test('should click on the add new product button', () => client.addNewProduct());
  test('open all category', () => client.openAllCategory());
  test('should check first category Radio button', () => client.checkCategoryRadioButton(1, 1));
  test('should check second category Radio button', () => client.checkCategoryRadioButton(1, 2));
  test('should check third Radio button', () => client.checkCategoryRadioButton(1, 3));
  test('should check fourth Radio button', () => client.checkCategoryRadioButton(2, 1));
  test('should check fifth category Radio button', () => client.checkCategoryRadioButton(2, 2));
  test('should check sixth category Radio button', () => client.checkCategoryRadioButton(2, 3));
}, 'product/product', true);
