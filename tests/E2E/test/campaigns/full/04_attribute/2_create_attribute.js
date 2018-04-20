const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonAttribute = require('../../common_scenarios/attribute');
const commonScenarios = require('../../common_scenarios/product');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();
const {ProductList} = require('../../../selectors/BO/add_product_page');

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

  /* Check the created attributes in the Front Office */
  scenario('Go to the Front Office', client => {
    test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData.name, attributeData);

  /* Update the created attribute */
  scenario('Go back to the Back Office', client => {
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  for (let i = 0; i < attributeData.length; i++) {
    commonAttribute.deleteAttribute(attributeData[i]);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);