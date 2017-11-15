scenario('Create product with combination', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to product menu', () => client.goToProductMenu());
  test('should click on the add new product button', () => client.addNewProduct());

  scenario('Edit Basic settings', client => {
    test('should set the name of product', () => client.setProductName('combination'));
    test('should select pack product type', () => client.setProductType('combination'));
    test('should enter the price of product', () => client.setPrice());
    test('should upload the picture of product', () => client.uploadPicture('image_test.jpg'));
    test('should click on create category button', () => client.addCategory());
    test('should enter the category name', () => client.setCategoryName('combination'));
    test('should click on category create button', () => client.createCategory());
    test('should remove home category', () => client.removeHomeCategory());
    test('should click on add brand button', () => client.addBrand('combination'));
    test('should select a brand', () => client.selectBrand());
    test('should click on add a related product button', () => client.addRelatedProduct('combination'));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should add feature height', () => client.addFeatureHeight('combination'));
    test('should enter the product price tax excluded', () => client.addProductPriceTaxExcluded());
    test('should enter the product reference', () => client.addProductReference());
    test('should make the product on line', () => client.productOnline());
  }, 'product/editbasicsettings');

  scenario('Edit product shipping', client => {
    test('should go to the product shipping form', () => client.goToProductShipping());
    test('should enter the shipping width', () => client.shippingWidth());
    test('should enter the shipping height', () => client.shippingHeight());
    test('should enter the shipping depth', () => client.shippingDepth());
    test('should enter the shipping weight', () => client.shippingWeight());
    test('should enter the additional shipping costs', () => client.shippingCosts());
    test('should select the available carrier', () => client.selectAvailableCarrier());
  }, 'product/editshipping');

  scenario('Create product combinations', client => {
    test('should go to the product combinations form', () => client.goToProductCombinationsForm());
    test('should create first combination', () => client.createFirstCombination());
    test('should create second combination', () => client.createSecondCombination());
    test('should get combination data', () => client.getCombinationData(1));
    test('should go to edit first combination', () => client.goToEditCombination());
    test('should edit first combination', () => client.editCombination(1));
    test('should go back to combination list', () => client.backToProduct());
    test('should get combination data', () => client.getCombinationData(2));
    test('should go to edit second combination', () => client.goToEditCombination());
    test('should edit second combination', () => client.editCombination(2));
    test('should go back to combination list', () => client.backToProduct());
    test('should select the availability preferences', () => client.availabilityPreferences());
    test('should enter the available label in stock', () => client.availabilityLabelInStock());
    test('should enter the available label out of stock', () => client.availabilityLabelOutStock());
  }, 'product/createcombinations');
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

scenario('Check the product in the catalog', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to the catalog', () => client.goToCatalog('combination'));
  test('should search the product by name', () => client.searchProductByName());
  test('should check the product name', () => client.checkProductName());
  test('should check the product reference', () => client.checkProductReference());
  test('should check the product category', () => client.checkProductCategory());
  test('should check the product price TE', () => client.checkProductPriceTE());
  test('should check the product quantity', () => client.checkProductQuantityCombination());
  test('should check the product status', () => client.checkProductStatus());
  test('should reset filter', () => client.resetFilter());
}, 'product/checkproduct', true);
