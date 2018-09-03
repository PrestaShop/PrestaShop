const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
let data = require('./../../../datas/product-data');
let promise = Promise.resolve();
global.productVariations = [];

scenario('Create product with combination in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should click on "NEW PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));

  scenario('Edit Basic settings', client => {
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, data.standard.name + 'C' + date_time));
    test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, data.common.summary));
    test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
    test('should set the "Description" text', () => client.setEditorText(AddProductPage.description_textarea, data.common.description));
    test('should select the "Pack of products"', () => client.waitForExistAndClick(AddProductPage.product_combinations));
    test('should upload the first product picture', () => client.uploadPicture('1.png', AddProductPage.picture));
    test('should upload the second product picture', () => client.uploadPicture('2.jpg', AddProductPage.picture));
    test('should click on "CREATE A CATEGORY"', () => client.scrollWaitForExistAndClick(AddProductPage.product_create_category_btn, 50));
    test('should set the "New category name"', () => client.waitAndSetValue(AddProductPage.product_category_name_input, data.standard.new_category_name + 'C' + date_time));
    test('should click on "Create"', () => client.createCategory());
    test('open all category', () => client.openAllCategory());
    test('should choose the created category as default', () => {
      return promise
        .then(() => client.waitForVisible(AddProductPage.created_category))
        .then(() => client.waitForExistAndClick(AddProductPage.home_delete_button));
    });
    test('should click on "ADD A BRAND" button', () => client.scrollWaitForExistAndClick(AddProductPage.product_add_brand_btn, 50));
    test('should select brand', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select))
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select_option));
    });
    test('should click on "ADD RELATED PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.add_related_product_btn));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    test('should click on "ADD A FEATURE" and select one', () => client.addFeatureHeight('combination'));
    test('should set "Tax exclude" price', () => client.setPrice(AddProductPage.priceTE_shortcut, data.common.priceTE));
    test('should set the "Reference" input', () => client.waitAndSetValue(AddProductPage.product_reference, data.common.product_reference));
    test('should switch the product online', () =>  {
      return promise
        .then(() => client.isVisible(AddProductPage.symfony_toolbar))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        })
        .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000));
    });
  }, 'product/product');

  scenario('Edit product shipping', client => {
    test('should click on "Shipping" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_shipping_tab, 50));
    test('should set the "Width" input', () => client.waitAndSetValue(AddProductPage.shipping_width, data.common.cwidth));
    test('should set the "Height" input', () => client.waitAndSetValue(AddProductPage.shipping_height, data.common.cheight));
    test('should set the "Depth" input', () => client.waitAndSetValue(AddProductPage.shipping_depth, data.common.cdepth));
    test('should set the "Weight" input', () => client.waitAndSetValue(AddProductPage.shipping_weight, data.common.cweight));
    test('should set the "Does this product incur additional shipping costs?" input', () => client.waitAndSetValue(AddProductPage.shipping_fees, data.common.cadd_ship_coast));
    test('should click on "My carrier (Delivery next day!)" button', () => client.scrollWaitForExistAndClick(AddProductPage.shipping_available_carriers, 50));
  }, 'product/product');

  /**
   * This scenario is based on the bug described in this ticket
   * http://forge.prestashop.com/browse/BOOM-3165
   * http://forge.prestashop.com/browse/BOOM-4469
   **/
  scenario('Create product combinations', client => {
    test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
    test('should choose the size "S" and color "Grey"', () => {
      return promise
        .then(() => client.createCombination(AddProductPage.combination_size_s, AddProductPage.combination_color_grey))
        .then(() => client.getTextInVar(AddProductPage.combination_size_s, "first_size"))
        .then(() => client.getTextInVar(AddProductPage.combination_color_grey, "first_color"))
        .then(() => productVariations.push([tab["first_size"], tab["first_color"]]));
    });
    test('should click on "Generate" button', () => client.waitForExistAndClick(AddProductPage.combination_generate_button));
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-4202
     **/
    test('should check the appearance of the first generated combination ', () => client.waitForExist(AddProductPage.combination_first_table));
    /**** END ****/
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should choose the size "S" and color "Beige"', () => {
      return promise
        .then(() => client.createCombination(AddProductPage.combination_size_m, AddProductPage.combination_color_beige))
        .then(() => client.getTextInVar(AddProductPage.combination_size_m, "second_size"))
        .then(() => client.getTextInVar(AddProductPage.combination_color_beige, "second_color"))
        .then(() => productVariations.push([tab["second_size"], tab["second_color"]]));
    });
    test('should click on "Generate" button', () => client.waitForExistAndClick(AddProductPage.combination_generate_button));
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-4202
     **/
    test('should check the appearance of the second generated combination ', () => client.waitForExist(AddProductPage.combination_second_table));
    /**** END ****/
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Edit" first combination', () => {
      return promise
        .then(() => client.getCombinationData(1))
        .then(() => client.goToEditCombination());
    });
    test('should edit first combination', () => client.editCombination(1));
    test('should go back to combination list', () => client.backToProduct());
    test('should check that combination\'s quantity is equal to "20"', () => client.checkAttributeValue(AddProductPage.combination_attribute_quantity.replace('%NUMBER', combinationId), 'value', "20"));
    test('should check that combination\'s picture is well updated', () => client.checkAttributeValue(AddProductPage.combination_attribute_image.replace('%NUMBER', combinationId), 'src', title_image, 'contain'));
    test('should check that the "Impact on price (tax incl.) is equal to "15"', () => {
      return promise
        .then(() => client.goToEditCombination())
        .then(() => client.checkAttributeValue(AddProductPage.combination_priceTI.replace('%NUMBER', combinationId), 'value', "15"));
    });
    test('should go back to combination list', () => client.backToProduct());
    test('should click on "Edit" second combination', () => {
      return promise
        .then(() => client.getCombinationData(2))
        .then(() => client.goToEditCombination());
    });
    test('should edit second combination', () => client.editCombination(2));
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-4827
     **/
    test('should click on "Set as default combination" button', () => client.scrollWaitForExistAndClick(AddProductPage.default_combination.replace('%NUMBER', combinationId)));
    test('should go back to combination list', () => client.backToProduct());
    test('should check that the second combination is the default combination', () => client.isSelected(AddProductPage.combination_default_button.replace('%NUMBER', combinationId)));
    test('should check that combination\'s quantity is equal to "10"', () => client.checkAttributeValue(AddProductPage.combination_attribute_quantity.replace('%NUMBER', combinationId), 'value', "10"));
    test('should check that combination\'s picture is well updated', () => client.checkAttributeValue(AddProductPage.combination_attribute_image.replace('%NUMBER', combinationId), 'src', title_image, 'contain'));
    test('should check that the "Impact on price (tax incl.) is equal to "20"', () => {
      return promise
        .then(() => client.goToEditCombination())
        .then(() => client.checkAttributeValue(AddProductPage.combination_priceTI.replace('%NUMBER', global.combinationId), 'value', "20"));
    });
    test('should go back to combination list', () => client.backToProduct());
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-3704
     **/
    test('should set the "Impact on price" to "2,5"', () => {
      return promise
        .then(() => client.showElement("td.attribute-price", 1))
        .then(() => client.waitAndSetValue(AddProductPage.combination_impact_price_input.replace('%NUMBER', global.combinationId), '2,5'));
    });
    test('should click on "Basic settings" tab', () => client.scrollWaitForExistAndClick(AddProductPage.basic_settings_tab, 50));
    test('should set the "Tax exclude" price', () => {
      return promise
        .then(() => client.scrollTo(AddProductPage.priceTE_shortcut, 50))
        .then(() => client.waitAndSetValue(AddProductPage.priceTE_shortcut, data.common.priceTE));
    });
    test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
    test('should check that the final price is equal to "26.666666 €"', () => {
      return promise
        .then(() => client.showElement("td.attribute-finalprice", 1))
        .then(() => client.checkTextValue(AddProductPage.combination_final_price_span.replace('%NUMBER', global.combinationId), "26.666666"));
    });
    /**** END ****/
    test('should click on "Availability preferences" button', () => client.scrollWaitForExistAndClick(AddProductPage.combination_availability_preferences, 50));
    test('should set the available label in stock', () => client.waitAndSetValue(AddProductPage.combination_label_in_stock, data.common.qty_msg_stock));
    test('should set the available label out of stock', () => client.waitAndSetValue(AddProductPage.combination_label_out_stock, data.common.qty_msg_unstock));
  }, 'product/create_combinations');

    scenario('Edit product pricing', client => {
      test('should click on "Pricing" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
      test('should set the "Price per unit (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.unit_price, data.common.unitPrice));
      test('should set the "Unit" input', () => client.waitAndSetValue(AddProductPage.unity, data.common.unity));
      test('should set the "Price (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.pricing_wholesale, data.common.wholesale));
      test('should select the "Priority management"', () => client.selectPricingPriorities());
    }, 'product/product');

    scenario('Edit SEO information', client => {
      test('should click on "SEO" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_SEO_tab, 50));
      test('should set the "Meta title" input', () => client.waitAndSetValue(AddProductPage.SEO_meta_title, data.common.metatitle));
      test('should set the "Meta description" input', () => client.waitAndSetValue(AddProductPage.SEO_meta_description, data.common.metadesc));
      test('should set the "Friendly URL" input', () => client.waitAndSetValue(AddProductPage.SEO_friendly_url, data.common.shortlink));
    }, 'product/product');

    scenario('Edit product options', client => {
      test('should click on "Options" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
      test('should select the "Visibility"', () => client.waitAndSelectByValue(AddProductPage.options_visibility, 'both'));
      test('should click on "Web only (not sold in your retail store)" checkbox', () => client.waitForExistAndClick(AddProductPage.options_online_only));
      test('should select the "Condition"', () => client.selectCondition());
      test('should set the "ISBN" input', () => client.waitAndSetValue(AddProductPage.options_isbn, data.common.isbn));
      test('should set the "EAN-13" input', () => client.waitAndSetValue(AddProductPage.options_ean13, data.common.ean13));
      test('should set the "UPC" input', () => client.UPCEntry());
      test('should click on "ADD A CUSTOMIZAITION" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
      test('should set the customization field "Label"', () => client.waitAndSetValue(AddProductPage.options_first_custom_field_label, data.common.personalization.perso_text.name));
      test('should select the customization field "Type" Text', () => client.waitAndSelectByValue(AddProductPage.options_first_custom_field_type, '1'));
      test('should click on "Required" checkbox', () => client.waitForExistAndClick(AddProductPage.options_first_custom_field_require));
      test('should click on "ADD A CUSTOMIZAITION" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_customization_field_button, 50));
      test('should set the second customization field "Label"', () => client.waitAndSetValue(AddProductPage.options_second_custom_field_label, data.common.personalization.perso_file.name));
      test('should select the customization field "Type" File', () => client.waitAndSelectByValue(AddProductPage.options_second_custom_field_type, '0'));
      test('should click on "ATTACH A NEW FILE" button', () => client.scrollWaitForExistAndClick(AddProductPage.options_add_new_file_button, 50));
      test('should add a file', () => client.addFile(AddProductPage.options_select_file, 'image_test.jpg'), 50);
      test('should set the file "Title" input', () => client.waitAndSetValue(AddProductPage.options_file_name, data.common.document_attach.name));
      test('should set the file "Description" input', () => client.waitAndSetValue(AddProductPage.options_file_description, data.common.document_attach.desc));
      test('should add the previous added file', () => client.scrollWaitForExistAndClick(AddProductPage.options_file_add_button, 50));
    }, 'product/product');

    scenario('Save Product', client => {
      test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'product/product');
}, 'product/product', true);

scenario('Check the product creation in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should search for product by name', () => client.searchProductByName(data.standard.name + 'C' + date_time));
  test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, data.standard.name + 'C' + date_time));
  test('should check the existence of product reference', () => client.checkTextValue(AddProductPage.catalog_product_reference, data.common.product_reference));
  test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, data.standard.new_category_name + 'C' + date_time));
  test('should check the existence of product price TE', () => client.checkProductPriceTE(data.common.priceTE));
  test('should check the existence of product quantity Combination', () => client.checkTextValue(AddProductPage.catalog_product_quantity, (parseInt(data.standard.variations[0].quantity) + parseInt(data.standard.variations[1].quantity)).toString()));
  test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
  test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
}, 'product/check_product', true);

scenario('Check the product with combination in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Check that the product with combination is well displayed in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.standard.name + 'C' + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the product name is equal to "' + (data.standard.name + 'C' + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (data.standard.name + 'C' + date_time).toUpperCase()));
    test('should check that the product price is equal to "€27.00"', () => client.checkTextValue(productPage.product_price, '€27.00'));
    test('should set the product size to "S"', () => client.waitAndSelectByAttribute(productPage.product_size, 'title', productVariations[0][0], 3000));
    test('should check that the product color is equal to "Grey"', () => client.checkTextValue(productPage.product_color, productVariations[0][1]));
    test('should set the product size to "M"', () => client.waitAndSelectByAttribute(productPage.product_size, 'title', productVariations[1][0], 3000));
    test('should check that the product color is equal to "Beige"', () => client.checkTextValue(productPage.product_color, productVariations[1][1]));
    test('should check that the product quantity us equal to "20"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', '10'));
    test('should check that the "summary" is equal to "' + data.common.summary + '"', () => client.checkTextValue(productPage.product_summary, data.common.summary));
    test('should check that the "description" is equal to "' + data.common.description + '"', () => client.checkTextValue(productPage.product_description, data.common.description));
    test('should check that the product reference is equal to "' + data.common.product_reference + '"', () => {
      return promise
        .then(() => client.waitForExistAndClick(productPage.product_detail_tab, 2000))
        .then(() => client.scrollTo(productPage.product_detail_tab, 180))
        .then(() => client.pause(2000))
        .then(() => client.checkTextValue(productPage.product_reference, data.common.product_reference));
    });
  }, 'product/product');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => {
      return promise
        .then(() => client.scrollTo(AccessPageFO.sign_out_button))
        .then(() => client.signOutFO(AccessPageFO));
    });
  }, 'product/product');
}, 'product/product', true);
