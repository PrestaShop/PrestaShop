const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
let data = require('./../../../datas/product-data');
let promise = Promise.resolve();

scenario('Create Standard Product in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open browser', async () => {
      await client.open();
      await client.startTracing('FullCreateStandardProductBO');
    });
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Edit Basic settings', client => {
    test('should go to "Catalog" page', () => client.goToLink(Menu.Sell.Catalog.products_submenu));
    test('should click on "NEW PRODUCT"', () => client.goToLink(AddProductPage.new_product_button));
    test('should set the "product name"', () => page.type(AddProductPage.product_name_input, data.standard.name + date_time));
    test('should set the "Quantity" of product', () => client.fillInputText(AddProductPage.quantity_shortcut_input, "10"));
    test('should set the "Summary" text', () => client.fillTextArea(AddProductPage.summary_textarea, data.common.summary));
    test('should click on "Description" tab', () => page.$eval(AddProductPage.tab_description, elem => elem.click()));
    test('should set the "Description" text', () => client.fillTextArea(AddProductPage.description_textarea, data.common.description));
    test('should upload the first product picture', () => client.uploadPicture('1.png', AddProductPage.picture));
    test('should upload the second product picture', () => client.uploadPicture('2.jpg', AddProductPage.picture));
    test('should upload the third product picture', () => client.uploadPicture('3.jpg', AddProductPage.picture));
    test('should click on "CREATE A CATEGORY"', () => page.$eval(AddProductPage.product_create_category_btn, elem => elem.click()));
    test('should upload the second product picture', () => client.uploadPicture('2.jpg', AddProductPage.picture));
    test('should set the "New category name"', () => client.fillInputText(AddProductPage.product_category_name_input, data.standard.new_category_name + date_time));
    test('should click on "Create" button', () => page.$eval(AddProductPage.category_create_btn, elem => elem.click()));
    test('should choose the created category as default', () => {
      return promise
        .then(() => client.waitForVisible(AddProductPage.created_category))
        .then(() => page.$eval(AddProductPage.home_delete_button, elem => elem.click()));
    });

    // test('should open all categories', () => client.openAllCategories()); //TODO: Verify if we should close then open all categories
    test('should check the existence of the first category Radio button', () => client.checkCategoryRadioButton(4));
    test('should check the existence of the second category Radio button', () => client.checkCategoryRadioButton(5));
    test('should check the existence of the third Radio button', () => client.checkCategoryRadioButton(7));
    test('should check the existence of the fourth Radio button', () => client.checkCategoryRadioButton(9));
    test('should click on "ADD A BRAND"', () => page.$eval(AddProductPage.product_add_brand_btn, elem => elem.click()));
    test('should select brand', () => {
      return promise
        .then(() => page.click(AddProductPage.product_brand_select))
        .then(() => page.waitForSelector(AddProductPage.product_brand_select_option))
        .then(() => page.click(AddProductPage.product_brand_select_option));

    });
    test('should click on "ADD RELATED PRODUCT"', () => page.$eval(AddProductPage.add_related_product_btn, elem => elem.click()));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should click on "ADD A FEATURE" and select one', () => client.addFeature('standard'));
    test('should set the "Tax exclude" price', () => client.fillInputText(AddProductPage.priceTE_shortcut, data.common.priceTE));
    test('should set the "Reference"', () => client.fillInputText(AddProductPage.product_reference, data.common.product_reference));
    test('should switch the product online', () => {
      return promise
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
        .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000))
    });
  }, 'product/product');

  scenario('Edit product quantity', client => {
    test('should click on "Quantities"', () => page.$eval(AddProductPage.product_quantities_tab, elem => elem.click()));
    test('should set the "Quantity"', () => client.fillInputText(AddProductPage.product_quantity_input, data.common.quantity));
    test('should set the "Minimum quantity for sale"', () => client.fillInputText(AddProductPage.minimum_quantity_sale, data.common.qty_min));
    test('should click on "Deny orders"', () => page.$eval(AddProductPage.pack_availability_preferences, elem => elem.click()));
    test('should set the "label when in stock"', () => client.fillInputText(AddProductPage.pack_label_in_stock, data.common.qty_msg_stock));
    test('should set the "Label when out of stock (and back order allowed)"', () => client.fillInputText(AddProductPage.pack_label_out_stock, data.common.qty_msg_unstock));
    test('should set the "Availability date"', () => client.fillInputText(AddProductPage.pack_availability_date, data.common.qty_date));
  }, 'product/product');


  scenario('Edit product shipping', client => {
    test('should click on "Shipping"', () => page.$eval(AddProductPage.product_shipping_tab, elem => elem.click()));
    test('should set the "Width"', () => client.fillInputText(AddProductPage.shipping_width, data.common.cwidth));
    test('should set the "Height"', () => client.fillInputText(AddProductPage.shipping_height, data.common.cheight));
    test('should set the "Depth"', () => client.fillInputText(AddProductPage.shipping_depth, data.common.cdepth));
    test('should set the "Weight"', () => client.fillInputText(AddProductPage.shipping_weight, data.common.cweight));
    test('should set the "Does this product incur additional shipping costs?"', () => client.fillInputText(AddProductPage.shipping_fees, data.common.cadd_ship_coast));
    test('should click on "My carrier (Delivery next day!)"', () => page.$eval(AddProductPage.shipping_available_carriers, elem => elem.click()));
  }, 'product/product');

  scenario('Edit the product pricing', client => {
    test('should click on "Pricing"', () => page.$eval(AddProductPage.product_pricing_tab, elem => elem.click()));
    test('should set the "Price per unit (tax excl.)"', () => client.fillInputText(AddProductPage.unit_price, data.common.unitPrice));
    test('should set the "Unit"', () => client.fillInputText(AddProductPage.unity, data.common.unity));
    test('should set the "Price (tax excl.)"', () => client.fillInputText(AddProductPage.pricing_wholesale, data.common.wholesale));
    test('should select the "Priority management"', () => client.selectPricingPriorities());
  }, 'product/product');

  scenario('Edit SEO information', client => {
    test('should click on "SEO"', () => page.$eval(AddProductPage.product_SEO_tab, elem => elem.click()));
    test('should set the "Meta title"', () => client.fillInputText(AddProductPage.SEO_meta_title, data.common.metatitle));
    test('should set the "Meta description"', () => client.fillInputText(AddProductPage.SEO_meta_description, data.common.metadesc));
    test('should set the "Friendly URL"', () => client.fillInputText(AddProductPage.SEO_friendly_url, data.common.shortlink));
  }, 'product/product');

  scenario('Edit product options', client => {
    test('should click on "Options"', () => page.$eval(AddProductPage.product_options_tab, elem => elem.click()));
    test('should select the "Visibility"', () => client.waitAndSelectByValue(AddProductPage.options_visibility, 'both'));
    test('should click on "Web only (not sold in your retail store)"', () => page.$eval(AddProductPage.options_online_only, elem => elem.click()));
    test('should select the "Condition"', () => client.waitAndSelectByValue(AddProductPage.options_condition_select, 'refurbished'));
    test('should set the "ISBN"', () => client.fillInputText(AddProductPage.options_isbn, data.common.isbn));
    test('should set the "EAN-13"', () => client.fillInputText(AddProductPage.options_ean13, data.common.ean13));
    test('should set the "UPC"', () => client.fillInputText(AddProductPage.options_upc, data.common.upc));
    test('should click on "ADD A CUSTOMIZAITION FIELD"', () => page.$eval(AddProductPage.options_add_customization_field_button, elem => elem.click()));
    test('should set the customization field "Label"', () => client.fillInputText(AddProductPage.options_first_custom_field_label, data.common.personalization.perso_text.name));
    test('should select the customization field "Type" Text', () => client.waitAndSelectByValue(AddProductPage.options_first_custom_field_type, '1'));
    test('should click on "Required"', () => page.$eval(AddProductPage.options_first_custom_field_require, elem => elem.click()));
    test('should click on "ADD A CUSTOMIZAITION"', () => page.$eval(AddProductPage.options_add_customization_field_button, elem => elem.click()));
    test('should set the second customization field "Label"', () => client.fillInputText(AddProductPage.options_second_custom_field_label, data.common.personalization.perso_file.name));
    test('should select the customization field "Type" File', () => client.waitAndSelectByValue(AddProductPage.options_second_custom_field_type, '0'));
    test('should click on "ATTACH A NEW FILE"', () => page.$eval(AddProductPage.options_add_new_file_button, elem => elem.click()));
    test('should add a file', () => client.uploadPicture('image_test.jpg',AddProductPage.options_select_file));
    test('should set the file "Title"', () => client.fillInputText(AddProductPage.options_file_name, data.common.document_attach.name));
    test('should set the file "Description" ', () => client.fillInputText(AddProductPage.options_file_description, data.common.document_attach.desc));
    test('should add the previous added file', () => page.$eval(AddProductPage.options_file_add_button, elem => elem.click()));
  }, 'product/product');

  scenario('Save Product', client => {
    test('should click on "SAVE"', () => page.$eval(AddProductPage.save_product_button, elem => elem.click()));
    test('should check that the success alert message is well displayed', () => page.waitForSelector(AddProductPage.close_validation_button,{ visible : true, }));
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'product/product', true);

scenario('Check the product creation in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToLink(Menu.Sell.Catalog.products_submenu));
  test('should search for product by name', () => client.searchProductByName(data.standard.name + date_time));
  test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, data.standard.name + date_time));
  test('should check the existence of product reference', () => client.checkTextValue(AddProductPage.catalog_product_reference, data.common.product_reference));
  test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, data.standard.new_category_name + date_time));
  test('should check the existence of product price TE', () => client.checkProductPriceTE(data.common.priceTE));
  test('should check the existence of product quantity', () => client.checkTextValue(AddProductPage.catalog_product_quantity, data.common.quantity));
  test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
  test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
}, 'product/check_product', true);


scenario('Check the standard product in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');

  scenario('Check that the standard product is well displayed in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.standard.name + date_time));
    test('should go to the product page', () => {
      return promise
        .then(()=> page.waitForSelector(SearchProductPage.product_result_name))
        .then(() => page.$eval(SearchProductPage.product_result_name, elem => elem.click()))
    });
    test('should check that the product name is equal to "' + (data.standard.name + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (data.standard.name + date_time).toUpperCase()));
    test('should check that the product price is equal to "€12.00"', () => client.checkTextValue(productPage.product_price, '€12.00'));
    test('should check that the product quantity is equal to "10"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', data.common.quantity));
    test('should check that the "summary" is equal to "' + data.common.summary + '"', () => client.checkTextValue(productPage.product_summary, data.common.summary));
    test('should check that the "description" is equal to "' + data.common.description + '"', () => client.checkTextValue(productPage.product_description, data.common.description));
    test('should check that the product reference is equal to "' + data.common.product_reference + '"', () => {
      return promise
        .then(() => page.$eval(productPage.product_detail_tab, elem => elem.click()))
        .then(() => page.waitForSelector(productPage.product_reference))
        .then(() => client.checkTextValue(productPage.product_reference, data.common.product_reference))
    });
  }, 'product/product');

  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'product/product');
}, 'product/product', true);
