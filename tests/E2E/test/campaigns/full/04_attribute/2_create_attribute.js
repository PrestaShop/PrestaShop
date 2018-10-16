const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonAttribute = require('../../common_scenarios/attribute');
const commonScenarios = require('../../common_scenarios/product');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');

let promise = Promise.resolve();

let productData = {
  name: 'PrAt',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'att',
  attribute: {
    1: {
      name: 'first_attribute',
      variation_quantity: '100'
    },
    2: {
      name: 'second_attribute',
      variation_quantity: '100'
    },
    3: {
      name: 'third_attribute',
      variation_quantity: '100'
    }
  }
};

let attributeData = [{
  name: 'first_attribute',
  public_name: 'first_attribute',
  type: 'select',
  values: {
    1: {
      value: '1'
    },
    2: {
      value: '2'
    },
    3: {
      value: '3'
    }
  }
}, {
  name: 'second_attribute',
  public_name: 'second_attribute',
  type: 'radio',
  values: {
    1: {
      value: '1'
    },
    2: {
      value: '2'
    },
    3: {
      value: '3'
    }
  }
}, {
  name: 'third_attribute',
  public_name: 'third_attribute',
  type: 'color',
  values: {
    1: {
      value: 'blanc',
      color: '#fffffc'
    },
    2: {
      value: 'gray texture',
      file: 't_shirt_gris.jpg'
    }
  }
}];

scenario('Create "Attributes" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');

  /* Create three type of attribute */
  for (let i = 0; i < attributeData.length; i++) {
    commonAttribute.createAttribute(attributeData[i]);
  }

  /* Create product with combination and add the created attributes */
  commonScenarios.createProduct(AddProductPage, productData, attributeData);

  scenario('Check the product pagination in the Back Office', client => {
    test('should go to "Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should get the product number', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products, 3000))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => {
          if (global.ps_mode_dev) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar)
          }
        });
    });
    test('should go to "Shop Parameters - Product Settings" page', () => {
      return promise
        .then(() => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu))
        .then(() => client.pause(3000))
        .then(() => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
    });
    test('should get the pagination Products per page value and check the created product in the Front Office', () => {
      return promise
        .then(() => client.getAttributeInVar(ProductSettings.Pagination.products_per_page_input, "value", "pagination"))
        .then(() => {
          global.pagination = Number(Math.trunc(Number(global.productsNumber) / Number(global.tab['pagination'])));
        })
    });
    /* Check the created attributes in the Front Office */
    test('should go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname, 3000))
        .then(() => client.switchWindow(1))
    });
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should click on "SEE ALL PRODUCTS" link', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(productPage.see_all_products))

    });
    test('should check all attribute type in the "Front Office"', () => commonAttribute.checkAllAttributeTypeInFO(AccessPageBO, productPage, productData.name, attributeData, client));

    test('should go to the product page', () => client.switchWindow(2));
    test('should check all attributes name of the product', () => client.checkTextValue(SearchProductPage.attribute_name, Object.keys(attributeData).map((k) => attributeData[k].name + date_time), 'deepequal'));
    Object.keys(attributeData).forEach(function (key) {
      if (attributeData[key].type === 'select') {
        test('should check the attribute select values', () => client.checkTextValue(SearchProductPage.attribute_select_values.replace('%ID', global.tab[attributeData[key].name + "_id"]), Object.keys(attributeData[key].values).map((k) => attributeData[key].values[k].value), 'deepequal'));
      } else if (attributeData[key].type === 'radio') {
        test('should check the attribute radio values', () => client.checkTextValue(SearchProductPage.attribute_radio_values, Object.keys(attributeData[key].values).map((k) => attributeData[key].values[k].value), 'deepequal'));
      } else if (attributeData[key].type === 'color') {
        test('should check the attribute color values', () => client.checkTextValue(SearchProductPage.attribute_color_and_texture_values, Object.keys(attributeData[key].values).map((k) => attributeData[key].values[k].value), 'deepequal'));
      }
    });
    test('should go back to the Back Office', () => client.switchWindow(0));
  }, 'product/product');
  commonAttribute.deleteAttribute(attributeData[0]);
  commonAttribute.deleteAttribute(attributeData[1]);
  commonAttribute.deleteAttribute(attributeData[2]);
}, 'attribute_and_feature', true);
