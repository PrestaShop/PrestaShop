const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();
const {AddProductPage} = require('../../selectors/BO/add_product_page');
let data = require('../../datas/product-data');

global.productVariations = [];

module.exports = {

  createCombinations(client) {
    test('should choose the size "S"', () => client.waitForExistAndClick(AddProductPage.combination_size_s));
    test('should choose the size "M"', () => client.waitForExistAndClick(AddProductPage.combination_size_m));
    test('should select the "Taupe" color ', () => client.waitForExistAndClick(AddProductPage.combination_color.replace('%ID', 6)));
    test('should select the "Black" color ', () => client.waitForExistAndClick(AddProductPage.combination_color.replace('%ID', 11)));
    test('should select the "Grey" color ', () => client.waitForExistAndClick(AddProductPage.combination_color.replace('%ID', 5)));
    test('should select the "Orange" color ', () => client.waitForExistAndClick(AddProductPage.combination_color.replace('%ID', 13)));
    test('should click on "Generate" button', () => client.waitForExistAndClick(AddProductPage.combination_generate_button));
    /**
     * This scenario is based on the bug described in this ticket
     * http://forge.prestashop.com/browse/BOOM-4202
     **/
    test('should check the appearance of the generated combinations table ', () => client.waitForExist(AddProductPage.combination_table));
    /**** END ****/
  },

  editCombinations(num) {
    scenario('Edit the combinations quantity', client => {
      for (let i = 1; i <= num; i++) {
        test('should set the combination quantity to "10"', () => {
          return promise
            .then(() => client.getCombinationData(i))
            .then(() => client.waitAndSetValue(AddProductPage.combination_attribute_quantity.replace('%NUMBER', global.combinationId), 10));
        });
        test('should set the price IT Value to "15"', () => {
          return promise
            .then(() => client.goToEditCombination())
            .then(() => client.scrollTo((AddProductPage.combination_priceTI.replace('%NUMBER', global.combinationId))))
            .then(() => client.waitAndSetValue(AddProductPage.combination_priceTI.replace('%NUMBER', global.combinationId), '15'));
        });
        if (i === 2) {
          test('should click on "Set as default combination" button', () => client.scrollWaitForExistAndClick(AddProductPage.default_combination.replace('%NUMBER', combinationId)));
        }
        test('should go back to combination list', () => client.backToProduct());
        if (i === 2) {
          /**
           * This scenario is based on the bug described in this ticket
           * http://forge.prestashop.com/browse/BOOM-4827
           **/
          test('should check that the second combination is the default combination', () => client.isSelected(AddProductPage.combination_default_button.replace('%NUMBER', combinationId)));
        }
        test('should check that combination\'s quantity is equal to "10"', () => client.checkAttributeValue(AddProductPage.combination_attribute_quantity.replace('%NUMBER', combinationId), 'value', "10"));
        test('should check that the "Impact on price (tax incl.) is equal to "15"', () => {
          return promise
            .then(() => client.goToEditCombination())
            .then(() => client.checkAttributeValue(AddProductPage.combination_priceTI.replace('%NUMBER', combinationId), 'value', "15"));
        });
        test('should go back to combination list', () => client.backToProduct());
      }
    }, 'product/create_combinations');
  },

  editPricing() {
    scenario('Edit product pricing', client => {
      test('should click on "Pricing" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
      test('should set the "Price per unit (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.unit_price, data.common.unitPrice));
      test('should set the "Unit" input', () => client.waitAndSetValue(AddProductPage.unity, data.common.unity));
      test('should set the "Price (tax excl.)" input', () => client.waitAndSetValue(AddProductPage.pricing_wholesale, data.common.wholesale));
      test('should select the "Priority management"', () => client.selectPricingPriorities());
    }, 'product/product')
  },

  editSeoInformations() {
    scenario('Edit SEO information', client => {
      test('should click on "SEO" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_SEO_tab, 50));
      test('should set the "Meta title" input', () => client.waitAndSetValue(AddProductPage.SEO_meta_title, data.common.metatitle));
      test('should set the "Meta description" input', () => client.waitAndSetValue(AddProductPage.SEO_meta_description, data.common.metadesc));
      test('should set the "Friendly URL" input', () => client.waitAndSetValue(AddProductPage.SEO_friendly_url, data.common.shortlink));
    }, 'product/product');
  },

  editProductOptions() {
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
  },

  editShipping() {
    scenario('Edit product shipping', client => {
      test('should click on "Shipping" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_shipping_tab, 50));
      test('should set the "Width" input', () => client.waitAndSetValue(AddProductPage.shipping_width, data.common.cwidth));
      test('should set the "Height" input', () => client.waitAndSetValue(AddProductPage.shipping_height, data.common.cheight));
      test('should set the "Depth" input', () => client.waitAndSetValue(AddProductPage.shipping_depth, data.common.cdepth));
      test('should set the "Weight" input', () => client.waitAndSetValue(AddProductPage.shipping_weight, data.common.cweight));
      test('should set the "Does this product incur additional shipping costs?" input', () => client.waitAndSetValue(AddProductPage.shipping_fees, data.common.cadd_ship_coast));
      test('should click on "My carrier (Delivery next day!)" button', () => client.scrollWaitForExistAndClick(AddProductPage.shipping_available_carriers, 50));
    }, 'product/product');
  },

  checkProductCreationBO(AccessPageBO, numberCombinations) {
    scenario('Check the product creation in the Back Office', client => {
      test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for product by name', () => client.searchProductByName(data.standard.name + 'C' + date_time));
      test('should check the existence of product name', () => client.checkTextValue(AddProductPage.catalog_product_name, data.standard.name + 'C' + date_time));
      test('should check the existence of product reference', () => client.checkTextValue(AddProductPage.catalog_product_reference, data.common.product_reference));
      test('should check the existence of product category', () => client.checkTextValue(AddProductPage.catalog_product_category, data.standard.new_category_name + 'C' + date_time));
      test('should check the existence of product price TE', () => client.checkProductPriceTE(data.common.priceTE));
      test('should check the existence of product quantity Combination', () => client.checkTextValue(AddProductPage.catalog_product_quantity, (numberCombinations * 10).toString()));
      test('should check the existence of product status', () => client.checkTextValue(AddProductPage.catalog_product_online, 'check'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/check_product', true);
  },

  /**
   * This scenario is based on the bug described in this ticket
   * http://forge.prestashop.com/browse/BOOM-3000
   **/

  checkCombinationProductFo(SearchProductPage, productPage, AccessPageFO) {
    scenario('Check that the product with combination is well displayed in the Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, data.standard.name + 'C' + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the product name is equal to "' + (data.standard.name + 'C' + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (data.standard.name + 'C' + date_time).toUpperCase()));
      test('should check that the product price is equal to "€27.00"', () => client.checkTextValue(productPage.product_price, '€27.00'));
      test('should set the product size to "S"', () => client.waitAndSelectByAttribute(productPage.product_size, 'title', 'S', 3000));
      test('should check that the product color is equal to "Grey"', () => client.checkTextValue(productPage.product_color, 'Grey'));
      test('should check that the product quantity us equal to "10"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', '10'));
      test('should check that the "summary" is equal to "' + data.common.summary + '"', () => client.checkTextValue(productPage.product_summary, data.common.summary));
      test('should check that the "description" is equal to "' + data.common.description + '"', () => client.checkTextValue(productPage.product_description, data.common.description));
      test('should check that the product reference is equal to "' + data.common.product_reference + '"', () => {
        return promise
          .then(() => client.waitForExistAndClick(productPage.product_tab_list.replace('%I', 2), 2000))
          .then(() => client.scrollTo(productPage.product_tab_list.replace('%I', 2), 180))
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
    }, 'product/product',true);
  }
};
