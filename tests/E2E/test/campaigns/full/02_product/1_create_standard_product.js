require('../../high/02_product/1_create_standard_product');

const {AddProductPage, ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
let data = require('./../../../datas/product-data');
let promise = Promise.resolve();

scenario('Check standard product information in the Back Office', () => {

  scenario('Open the browser and login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  scenario('Check the standard product information', client => {
    test('should go to "Catalog" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu));
    test('should search for the product by name', () => client.searchProductByName(data.standard.name + date_time));
    test('should click on "Edit" icon', () => client.waitForExistAndClick(ProductList.edit_button));

    scenario('Check the Basic settings information', client => {
      test('should check that the "Standard product" option is selected', () => client.isSelected(AddProductPage.standard_product_type_option));
      test('should check that the "name" is equal to "' + data.standard.name + date_time + '"', () => client.checkAttributeValue(AddProductPage.product_name_input, "value", data.standard.name + date_time));
      test('should check that the "Quantity" is equal to "' + data.common.quantity + '"', () => client.checkAttributeValue(AddProductPage.quantity_shortcut_input, "value", data.common.quantity));
      test('should check that the "category name" is equal to "' + data.standard.new_category_name + date_time + '"', () => client.checkTextValue(AddProductPage.category_value, data.standard.new_category_name + date_time, "contain"));
      test('should check the "Brand" selected option', () => client.isSelected(AddProductPage.brand_selected_option));
      test('should check that the first related product is equal to "' + data.common.search_related_products.split('//')[0] + '"', () => client.checkTextValue(AddProductPage.related_product.replace("%ID", 1), data.common.search_related_products.split('//')[0]));
      test('should check that the second related product is equal to "' + data.common.search_related_products.split('//')[1] + '"', () => client.checkTextValue(AddProductPage.related_product.replace("%ID", 2), data.common.search_related_products.split('//')[1]));
      test('should check that the "Tax excluded" is equal to "' + data.common.priceTE + '"', () => client.checkAttributeValue(AddProductPage.priceTE_shortcut, "value", data.common.priceTE, "contain"));
      test('should check that the "Reference" is equal to "' + data.common.product_reference + '"', () => client.checkAttributeValue(AddProductPage.product_reference, "value", data.common.product_reference, "contain"));
    }, 'product/product');

    scenario('Check the product quantity information', client => {
      test('should click on "Quantities" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_quantities_tab, 50));
      test('should check that the "Quantity" is equal to "' + data.common.quantity + '"', () => client.checkAttributeValue(AddProductPage.product_quantity_input, "value", data.common.quantity));
      test('should check that the "Minimum quantity for sale" is equal to "' + data.common.qty_min + '"', () => client.checkAttributeValue(AddProductPage.minimum_quantity_sale, "value", data.common.qty_min));
      test('should check that "Deny orders" option is checked', () => client.isSelected(AddProductPage.pack_availability_preferences));
      test('should check that the "label when in stock" is equal to "' + data.common.qty_msg_stock + '"', () => client.checkAttributeValue(AddProductPage.pack_label_in_stock, "value", data.common.qty_msg_stock));
      test('should check that the "Label when out of stock (and back order allowed)" is equal to "' + data.common.qty_msg_unstock + '"', () => client.checkAttributeValue(AddProductPage.pack_label_out_stock, "value", data.common.qty_msg_unstock));
      test('should check that the "Availability date" is equal to "' + data.common.qty_date + '"', () => client.checkAttributeValue(AddProductPage.pack_availability_date, "value", data.common.qty_date));
    }, 'product/product');

    scenario('Check the product shipping information', client => {
      test('should click on "Shipping" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_shipping_tab, 50));
      test('should check that the "Width" is equal to "' + data.common.cwidth + '"', () => client.checkAttributeValue(AddProductPage.shipping_width, "value", data.common.cwidth));
      test('should check that the "Height" is equal to "' + data.common.cheight + '"', () => client.checkAttributeValue(AddProductPage.shipping_height, "value", data.common.cheight));
      test('should check that the "Depth" is equal to "' + data.common.cdepth + '"', () => client.checkAttributeValue(AddProductPage.shipping_depth, "value", data.common.cdepth));
      test('should check that the "Weight" is equal to "' + data.common.cweight + '"', () => client.checkAttributeValue(AddProductPage.shipping_weight, "value", data.common.cweight));
      test('should check that the "Does this product incur additional shipping costs?" is equal to "' + data.common.cadd_ship_coast + '"', () => client.checkAttributeValue(AddProductPage.shipping_fees, "value", data.common.cadd_ship_coast, "contain"));
      test('should check that "Available carriers - My carrier" option is selected ', () => client.isSelected(AddProductPage.shipping_available_carriers));
    }, 'product/product');

    scenario('Check the product pricing information', client => {
      test('should click on "Pricing" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_pricing_tab, 50));
      test('should check that the "Price per unit (tax excl.)" is equal to "' + data.common.unitPrice + '"', () => client.checkAttributeValue(AddProductPage.unit_price, "value", data.common.unitPrice, "contain"));
      test('should check that the "Unit" is equal to "' + data.common.unity + '"', () => client.checkAttributeValue(AddProductPage.unity, "value", data.common.unity, "contain"));
      test('should check that the "Price (tax excl.)" is equal to "' + data.common.wholesale + '"', () => client.checkAttributeValue(AddProductPage.pricing_wholesale, "value", data.common.wholesale, "contain"));
      test('should check the "Priority management" option is selected', () => {
        return promise
          .then(() => client.isSelected(AddProductPage.priority_shop))
          .then(() => client.isSelected(AddProductPage.priority_currency))
          .then(() => client.isSelected(AddProductPage.priority_country))
          .then(() => client.isSelected(AddProductPage.priority_country));
      });
    }, 'product/product');

    scenario('Check the product SEO information', client => {
      test('should click on "SEO" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_SEO_tab, 50));
      test('should check that the "Meta title" is equal to "' + data.common.metatitle + '"', () => client.checkAttributeValue(AddProductPage.SEO_meta_title, "value", data.common.metatitle));
      test('should check that the "Meta description" is equal to "' + data.common.metadesc + '"', () => client.checkAttributeValue(AddProductPage.SEO_meta_description, "value", data.common.metadesc));
      test('should check that the "Friendly URL" is equal to "' + data.common.shortlink + '"', () => client.checkAttributeValue(AddProductPage.SEO_friendly_url, "value", data.common.shortlink));
    }, 'product/product');

    scenario('Check the product options information', client => {
      test('should click on "Options" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_options_tab));
      test('should check the "Visibility - Everywhere" option is selected ', () => client.isSelected(AddProductPage.visibility_selected_option));
      test('should check that "Web only (not sold in your retail store)" option is checked', () => client.isSelected(AddProductPage.options_online_only));
      test('should check the "Condition - Refurbished" option is selected', () => client.isSelected(AddProductPage.condition_selected_option));
      test('should check that the "ISBN" is equal to "' + data.common.isbn + '"', () => client.checkAttributeValue(AddProductPage.options_isbn, "value", data.common.isbn));
      test('should check that the "EAN-13" is equal to "' + data.common.ean13 + '"', () => client.checkAttributeValue(AddProductPage.options_ean13, "value", data.common.ean13));
      test('should check that the "UPC" is equal to "' + data.common.upc + '"', () => client.checkAttributeValue(AddProductPage.options_upc, "value", data.common.upc));
      test('should check that customization field "Label" is equal to "' + data.common.personalization.perso_text.name + '"', () => client.checkAttributeValue(AddProductPage.options_first_custom_field_label, "value", data.common.personalization.perso_text.name));
      test('should check that customization field "Type - Text" option is selected', () => client.isSelected(AddProductPage.selected_type));
      test('should check that "Required" option is checked', () => client.isSelected(AddProductPage.options_first_custom_field_require));
    }, 'product/product');

  }, 'product/check_product');

  scenario('Logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'product/product', true);