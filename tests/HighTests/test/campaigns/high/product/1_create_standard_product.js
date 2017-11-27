const {selector} = require('../../../globals.webdriverio.js');

scenario('Create Standard Product', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(selector));
  test('should go to "Catalog"', () => client.waitForExistAndClick(selector.BO.AddProductPage.products_subtab));
  test('should click on "NEW PRODUCT"', () => client.waitForExistAndClick(selector.BO.AddProductPage.new_product_button));

  scenario('Edit Basic settings', client => {
    test('should set the "product name"', () => client.setProductName('standard'));
    test('should set the "Quantity" of product', () => client.setQuantity());
    // test('should upload the picture one of product', () => client.uploadPicture('1.png'));   // not done
    // test('should upload the picture two of product', () => client.uploadPicture('2.jpg'));   // not done
    // test('should upload the picture three of product', () => client.uploadPicture('3.jpg')); // not done
    test('should click on "CREATE A CATEGORY"', () => client.addCategory());
    // test('should upload the picture two of product', () => client.uploadPicture('2.jpg'));
    test('should set the "New category name"', () => client.setCategoryName('standard'));
    test('should click on "Create"', () => client.createCategory());
    test('should remove "HOME" category', () => client.removeHomeCategory());
    test('should click on "ADD A BRAND"', () => client.addBrand('standard'));
    test('should select brand', () => client.selectBrand());
    test('should click on "ADD RELATED PRODUCT"', () => client.addRelatedProduct('standard'));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should click on "ADD A FEATURE" and select one', () => client.addFeatureHeight('standard'));
    test('should set "Tax exclude" price', () => client.addProductPriceTaxExcluded());
    test('should enter the product reference', () => client.addProductReference());
    test('should set the product "online"', () => client.productOnline());
  }, 'product/editbasicsettings');

  scenario('Edit product quantity', client => {
    test('should click on "Quantities"', () => client.goToProductQuantity());
    test('should set the "Quantity"', () => client.productQuantity());
    test('should set the "Minimum quantity for sale"', () => client.minQuantitySale());
    test('should click on "Deny orders"', () => client.selectAvailabilityPreferences('standard'));
    test('should set the "label when in stock"', () => client.availableStock());
    test('should set the "Label when out of stock (and back order allowed)"', () => client.availableOutOfStock());
    test('should set the "Availability date"', () => client.availabilityDate());
  }, 'product/editquantity');


  scenario('Edit product shipping', client => {
    test('should click on "Shipping"', () => client.goToProductShipping());
    test('should set the "Width"', () => client.shippingWidth());
    test('should set the "Height"', () => client.shippingHeight());
    test('should set the "Depth"', () => client.shippingDepth());
    test('should set the "Weight"', () => client.shippingWeight());
    test('should set the "Does this product incur additional shipping costs?"', () => client.shippingCosts());
    test('should click on "My carrier (Delivery next day!)"', () => client.selectAvailableCarrier());
  }, 'product/editshipping');


  scenario('Edit product pricing', client => {
    test('should click on "Pricing"', () => client.goToPricingTab());
    test('should set the "Price per unit (tax excl.)"', () => client.pricingUnity());
    test('should set the "Price (tax excl.)"', () => client.pricingWholesale());
    test('should select the "Priority management"', () => client.pricingPriorities());
  }, 'product/editpricing');

  scenario('Edit SEO information', client => {
    test('should click on "SEO"', () => client.goToSEOTab());
    test('should set the "Meta title"', () => client.metaTitle());
    test('should set the "Meta description"', () => client.metaDescription());
    test('should set the "Friendly URL"', () => client.friendlyUrl());
  }, 'product/editseo');

  scenario('Edit product options', client => {
    test('should click on "Options"', () => client.goToOptionsForm());
    test('should select the "Visibility"', () => client.selectVisibility());
    test('should click on "Web only (not sold in your retail store)"', () => client.webOnlyVisibility());
    test('should select the "Condition"', () => client.selectCondition());
    test('should set the "ISBN"', () => client.ISBNEntry());
    test('should set the "EAN-13"', () => client.EAN13Entry());
    test('should set the "UPC"', () => client.UPCEntry());
    test('should click on "ADD A CUSTOMIZAITION"', () => client.AddCustomFieldButton());
    test('should set "Label" ,"Type" and "Required"', () => client.createCustomField());
    test('should click on "ADD A CUSTOMIZAITION"', () => client.AddCustomFieldButton());
    test('should create new custom field', () => client.newCustomField());
    test('should click on attach a new file button', () => client.attachNewFile()); // not done
    test('should add a file', () => client.addFile('image_test.jpg')); // not done
    test('should select the previous added file', () => client.selectPreviousAddFile());
  }, 'product/editoptions');

  scenario('Save Product', client => {
    test('should click on "SAVE"', () => client.saveProduct());
    test('should close green validation', () => client.closeGreenValidation());
    test('should sign out BO', () => client.signOutBO());
  }, 'product/product');

}, 'product/product', true);

scenario('Check the product in the catalog', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(selector));
  test('should go to "Catalog"', () => client.goToCatalog('standard'));
  test('should search for product by name', () => client.searchProductByName());
  test('should check the existance of product name', () => client.checkProductName());
  test('should check the existance of product reference', () => client.checkProductReference());
  test('should check the existance of product category', () => client.checkProductCategory());
  test('should check the existance of product price TE', () => client.checkProductPriceTE());
  test('should check the existance of product quantity', () => client.checkProductQuantity());
  test('should check the existance of product status', () => client.checkProductStatus());
  test('should reset filter', () => client.resetFilter());
}, 'product/checkproduct', true);
