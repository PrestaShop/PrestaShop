const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonAttribute = require('../../common_scenarios/attribute');
const commonScenarios = require('../../common_scenarios/product');
const welcomeScenarios = require('../../common_scenarios/welcome');

let productData = {
  name: 'Att2',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'att',
  attribute: {
    1: {
      name: 'attribute',
      variation_quantity: '10'
    }
  }
};

let attributeData = {
  name: 'attribute',
  public_name: 'attribute',
  type: 'radio',
  values: {
    1: {
      value: '10'
    },
    2: {
      value: '20'
    },
    3: {
      value: '30'
    }
  }
};

require('../../high/04_attribute/1_create_edit_delete_attribute_value');

scenario('Delete "Attribute" with bulk actions', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonAttribute.createAttribute(attributeData);
  commonScenarios.createProduct(AddProductPage, productData);
  commonAttribute.attributeBulkActions(attributeData, 'delete');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkDeletedAttributeInFO(productData.name);

}, 'attribute_and_feature', true);
