const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
let data = require('./../../../datas/product-data');
let promise = Promise.resolve();

scenario('Create a pack of products in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should click on "NEW PRODUCT"', () => client.waitForExistAndClick(AddProductPage.new_product_button));

  scenario('Edit the Basic settings', client => {
    test('should set the "product name"', () => client.waitAndSetValue(AddProductPage.product_name_input, data.pack.name + date_time));
    test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, data.common.summary));
    test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
    test('should set the "Description" text', () => client.setEditorText(AddProductPage.description_textarea, data.common.description));
    test('should select the "Pack of products"', () => client.waitAndSelectByValue(AddProductPage.product_type, 1));
    test('should set the "Add products to your pack"', () => {
      return promise
        .then(() => client.waitAndSetValue(AddProductPage.search_product_pack, data.pack.pack.pack1.search))
        .then(() => client.waitForExistAndClick(AddProductPage.product_item_pack))
        .then(() => client.waitAndSetValue(AddProductPage.product_pack_item_quantity, data.pack.pack.pack1.quantity))
        .then(() => client.waitForExistAndClick(AddProductPage.product_pack_add_button));
    });
    test('should set the "Add products to your pack"', () => {
      return promise
        .then(() => client.scrollTo(AddProductPage.description_tab))
        .then(() => client.waitAndSetValue(AddProductPage.search_product_pack, data.pack.pack.pack2.search))
        .then(() => client.waitForExistAndClick(AddProductPage.product_item_pack))
        .then(() => client.waitAndSetValue(AddProductPage.product_pack_item_quantity, data.pack.pack.pack2.quantity))
        .then(() => client.scrollWaitForExistAndClick(AddProductPage.product_pack_add_button));
    });
    test('should set the "Quantity" of product', () => client.waitAndSetValue(AddProductPage.quantity_shortcut_input, "10"));
    test('should upload the product picture', () => client.uploadPicture('image_test.jpg', AddProductPage.picture));
    test('should click on "CREATE A CATEGORY"', () => client.scrollWaitForExistAndClick(AddProductPage.product_create_category_btn, 50));
    test('should set the "New category name"', () => client.waitAndSetValue(AddProductPage.product_category_name_input, data.pack.new_category_name + date_time));
    test('should click on "Create" button', () => client.createCategory());
  //  test('should open all categories', () => client.openAllCategories()); //TODO: Verify if we should close then open all categories
    test('should choose the created category as default', () => {
      return promise
        .then(() => client.waitForVisible(AddProductPage.created_category))
        .then(() => client.scrollWaitForExistAndClick(AddProductPage.home_delete_button));
    });
    test('should click on "ADD A BRAND"', () => client.scrollWaitForExistAndClick(AddProductPage.product_add_brand_btn, 50));
    test('should select brand', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select))
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select_option));

    });
    test('should click on "ADD RELATED PRODUCT"', () => client.scrollWaitForExistAndClick(AddProductPage.add_related_product_btn, 50));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should click on "ADD A FEATURE" and select one', () => client.addFeature('pack'));
    test('should set the "Tax exclude" price', () => client.setPrice(AddProductPage.priceTE_shortcut, data.common.priceTE));
    test('should set the "Reference"', () => client.waitAndSetValue(AddProductPage.product_reference, data.common.product_reference));
    test('should switch the product online', () =>  {
      return promise
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
        .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000))
    });
  }, 'product/product');

  scenario('Edit product quantities', client => {
    test('should click on "Quantities"', () => client.scrollWaitForExistAndClick(AddProductPage.product_quantities_tab, 50));
    test('should set the "Quantity"', () => client.waitAndSetValue(AddProductPage.product_quantity_input, data.common.quantity));
    test('should set the "Minimum quantity for sale"', () => client.waitAndSetValue(AddProductPage.minimum_quantity_sale, data.common.qty_min));
    test('should set the "Pack quantity"', () => client.waitAndSelectByValue(AddProductPage.pack_stock_type, '2'));
    test('should click on "Deny orders"', () => client.waitForExistAndClick(AddProductPage.pack_availability_preferences));
    test('should set the "label when in stock"', () => client.waitAndSetValue(AddProductPage.pack_label_in_stock, data.common.qty_msg_stock));
    test('should set the "Label when out of stock (and back order allowed)"', () => client.availability());
    test('should set the "Availability date"', () => client.waitAndSetValue(AddProductPage.pack_availability_date, data.common.qty_date));
  }, 'product/product');

  scenario('Edit product shipping', client => {
    test('should click on "Shipping"', () => client.scrollWaitForExistAndClick(AddProductPage.product_shipping_tab, 50));
    test('should set the "Width"', () => client.waitAndSetValue(AddProductPage.shipping_width, data.common.cwidth));
    test('should set the "Height"', () => client.waitAndSetValue(AddProductPage.shipping_height, data.common.cheight));
    test('should set the "Depth"', () => client.waitAndSetValue(AddProductPage.shipping_depth, data.common.cdepth));
    test('should set the "Weight"', () => client.waitAndSetValue(AddProductPage.shipping_weight, data.common.cweight));
    test('should set the "Does this product incur additional shipping costs?"', () => client.waitAndSetValue(AddProductPage.shipping_fees, data.common.cadd_ship_coast));
    test('should click on "My carrier (Delivery next day!)"', () => client.scrollWaitForExistAndClick(AddProductPage.shipping_available_carriers, 50));
  }, 'product/product');

  scenario('Edit product pricing', client => {
    test('should click on "Pricing"', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
    test('should set the "Price per unit (tax excl.)"', () => client.waitAndSetValue(AddProductPage.unit_price, data.common.unitPrice));
    test('should set the "Unit"', () => client.waitAndSetValue(AddProductPage.unity, data.common.unity));
    test('should set the "Price (tax excl.)"', () => client.waitAndSetValue(AddProductPage.pricing_wholesale, data.common.wholesale));
    test('should select the "Priority management"', () => client.selectPricingPriorities());
  }, 'product/product');

  scenario('Edit SEO information', client => {
    test('should click on "SEO"', () => client.scrollWaitForExistAndClick(AddProductPage.product_SEO_tab, 50));
    test('should set the "Meta title"', () => client.waitAndSetValue(AddProductPage.SEO_meta_title, data.common.metatitle));
    test('should set the "Meta description"', () => client.waitAndSetValue(AddProductPage.SEO_meta_description, data.common.metadesc));
    test('should set the "Friendly URL"', () => client.waitAndSetValue(AddProductPage.SEO_friendly_url, data.common.shortlink));
  }, 'product/product');

  scenario('Edit product options', client => {
    test('should click on "Options"', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
    test('should select the "Visibility"', () => client.waitAndSelectByValue(AddProductPage.options_visibility, 'both'));
    test('should click on "Web only (not sold in your retail store)"', () => client.waitForExistAndClick(AddProductPage.options_online_only));
    test('should select the "Condition"', () => client.selectCondition());
    test('should set the "ISBN"', () => client.waitAndSetValue(AddProductPage.options_isbn, data.common.isbn));
    test('should set the "EAN-13"', () => client.waitAndSetValue(AddProductPage.options_ean13, data.common.ean13));
    test('should set the "UPC"', () => client.UPCEntry());
    test('should click on "ADD A CUSTOMIZAITION"', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should set the customization field "Label"', () => client.waitAndSetValue(AddProductPage.options_first_custom_field_label, data.common.personalization.perso_text.name));
    test('should select the customization field "Type" Text', () => client.waitAndSelectByValue(AddProductPage.options_first_custom_field_type, '1'));
    test('should click on "Required"', () => client.waitForExistAndClick(AddProductPage.options_first_custom_field_require));
    test('should click on "ADD A CUSTOMIZAITION"', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
    test('should set the second customization field "Label"', () => client.waitAndSetValue(AddProductPage.options_second_custom_field_label, data.common.personalization.perso_file.name));
    test('should select the customization field "Type" File', () => client.waitAndSelectByValue(AddProductPage.options_second_custom_field_type, '0'));
    test('should click on "ATTACH A NEW FILE"', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_new_file_button, 50));
    test('should add a file', () => client.addFile(AddProductPage.options_select_file, 'image_test.jpg'), 50);
    test('should set the file "Title"', () => client.waitAndSetValue(AddProductPage.options_file_name, data.common.document_attach.name));
    test('should set the file "Description" ', () => client.waitAndSetValue(AddProductPage.options_file_description, data.common.document_attach.desc));
    test('should add the previous added file', () => client.scrollWaitForExistAndClick(AddProductPage.options_file_add_button, 50));
  }, 'product/product');

  scenario('Save Product', client => {
    test('should click on "SAVE"', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');
}, 'product/product', true);

scenario('Check the product creation in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should search for product by name', () => client.searchProductByName(data.pack.name + date_time));
  test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, data.pack.name + date_time));
  test('should check the existence of product reference', () => client.checkTextValue(AddProductPage.catalog_product_reference, data.common.product_reference));
  test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, data.pack.new_category_name + date_time));
  test('should check the existence of product price TE', () => client.checkProductPriceTE(data.common.priceTE));
  test('should check the existence of product quantity', () => client.checkTextValue(AddProductPage.catalog_product_quantity, data.common.quantity));
  test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
  test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
}, 'product/check_product', true);

scenario('Check the pack product in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Check that the pack product is well displayed in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.pack.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the product name is equal to "' + (data.pack.name + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (data.pack.name + date_time).toUpperCase()));
    test('should check that the product price is equal to "€12.00"', () => client.checkTextValue(productPage.product_price, '€12.00'));
    test('should check that the first product pack name is equal to "The adventure begins Framed poster Dimension-40x60cm"', () => client.checkTextValue(productPage.pack_product_name.replace('%P', 1), 'The adventure begins Framed poster Dimension-40x60cm'));
    test('should check that the first product pack price is equal to "€34.80"', () => client.checkTextValue(productPage.pack_product_price.replace('%P', 1), '€34.80'));
    test('should check that the first product pack quantity is equal to "1"', () => client.checkTextValue(productPage.pack_product_quantity.replace('%P', 1), 'x 1'));
    test('should check that the second product pack name is equal to "Today is a good day Framed poster Dimension-40x60cm"', () => client.checkTextValue(productPage.pack_product_name.replace('%P', 2), 'Today is a good day Framed poster Dimension-40x60cm'));
    test('should check that the second product pack price is equal to "€34.80"', () => client.checkTextValue(productPage.pack_product_price.replace('%P', 2), '€34.80'));
    test('should check that the second product pack quantity is equal to "3"', () => client.checkTextValue(productPage.pack_product_quantity.replace('%P', 2), 'x 3'));
    test('should check that the product quantity is equal to "10"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', data.common.quantity));
    test('should check that the "summary" is equal to "' + data.common.summary + '"', () => client.checkTextValue(productPage.product_summary, data.common.summary));
    test('should check that the "description" is equal to "' + data.common.description + '"', () => client.checkTextValue(productPage.product_description, data.common.description));
    test('should check that the product reference is equal to "' + data.common.product_reference + '"', () => {
      return promise
        .then(() => client.waitForExistAndClick(productPage.product_detail_tab, 2000))
        .then(() => client.scrollTo(productPage.product_detail_tab, 180))
        .then(() => client.pause(2000))
        .then(() => client.checkTextValue(productPage.product_reference, data.common.product_reference))
    });
  }, 'product/product');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => {
      return promise
        .then(() => client.scrollTo(AccessPageFO.sign_out_button))
        .then(() => client.signOutFO(AccessPageFO))
    });
  }, 'product/product');
}, 'product/product', true);
