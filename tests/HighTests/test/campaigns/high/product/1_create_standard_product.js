scenario('Create Standard Product', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to product menu', () => client.goToProductMenu());
  test('should click on the add new product button', () => client.addNewProduct());

  scenario('Edit Basic settings', client => {
    test('should set the name of product', () => client.setProductName('standard'));
    test('should set the quantity of product', () => client.setQuantity());
    test('should set the price of product', () => client.setPrice());
    test('should upload the picture one of product', () => client.uploadPicture('1.png'));
    test('should upload the picture two of product', () => client.uploadPicture('2.jpg'));
    test('should upload the picture three of product', () => client.uploadPicture('3.jpg'));
    test('should click on add category button', () => client.addCategory());
    test('should set the category name', () => client.setCategoryName('standard'));
    test('should click on create category button', () => client.createCategory());
    test('should remove home category tag', () => client.removeHomeCategory());
    test('should click on add brand button', () => client.addBrand('standard'));
    test('should select brand', () => client.selectBrand());
    test('should click on add a related product button', () => client.addRelatedProduct('standard'));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should add feature height', () => client.addFeatureHeight('standard'));
    test('should enter the product price tax excluded', () => client.addProductPriceTaxExcluded());
    test('should enter the product reference', () => client.addProductReference());
    test('should make the product on line', () => client.productOnline());
  }, 'product/editbasicsettings');

  scenario('Edit product quantities', client => {
    test('should go to the product quantities form', () => client.goToProductQuantity());
    test('should enter the product quantity', () => client.productQuantity());
    test('should enter the minimum quantity for sale', () => client.minQuantitySale());
    test('should select the availability preferences', () => client.selectAvailabilityPreferences('standard'));
    test('should enter the available label in stock', () => client.avaibleStock());
    test('should enter the available label out of stock', () => client.availableOutOfStock());
    test('should enter the availability date', () => client.availabilityDate());
  }, 'product/editquantity');

  scenario('Edit product shipping', client => {
    test('should go to the product shipping form', () => client.goToProductShipping());
    test('should enter the shipping width', () => client.shippingWidth());
    test('should enter the shipping height', () => client.shippingHeight());
    test('should enter the shipping depth', () => client.shippingDepth());
    test('should enter the shipping weight', () => client.shippingWeight());
    test('should enter the additional shipping costs', () => client.shippingCosts());
    test('should select the available carrier', () => client.selectAvailableCarrier());
  }, 'product/editshipping');

  scenario('Edit product pricing', client => {
    test('should go to the product pricing tab', () => client.goToPricingTab());
    test('should enter the pricing unity', () => client.pricingUnity());
    test('should enter the pricing wholesale', () => client.pricingWholesale());
    test('should select the pricing priorities', () => client.pricingPriorities());
  }, 'product/editpricing');

  scenario('Edit SEO information', client => {
    test('should go to the product SEO form', () => client.goToSEOTab());
    test('should enter the meta title', () => client.metaTitle());
    test('should enter the meta description', () => client.metaDescription());
    test('should enter the friendly url', () => client.friendlyUrl());
  }, 'product/editseo');

  scenario('Edit product options', client => {
    test('should go to the product SEO form', () => client.goToOptionsForm());
    test('should select the visibility', () => client.selectVisibility());
    test('should enable the web only visibility', () => client.webOnlyVisibility());
    test('should select the condition', () => client.selectCondition());
    test('should enter the ISBN', () => client.ISBNEntry());
    test('should enter the EAN-13', () => client.EAN13Entry());
    test('should enter the UPC', () => client.UPCEntry());
    test('should click on customization button', () => client.customizationButton());
    test('should create new custom field', () => client.createCustomField());
    test('should click on add a customization field button', () => client.AddCustomFieldButton());
    test('should create new custom field', () => client.newCustomField());
    test('should click on attach a new file button', () => client.attachNewFile());
    test('should add a file', () => client.addFile('image_test.jpg'));
    test('should select the previous added file', () => client.selectPreviousAddFile());
  }, 'product/editoptions');

  scenario('Save Product', client => {
    test('should save and stay in the product page', () => client.saveProduct());
    test('should close green validation', () => client.closeGreenValidation());
    test('should sign out BO', () => client.signoutBO());
  }, 'product/product');

}, 'product/product', true);
