const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_attribute = require('../../common_scenarios/attribute');
const common_scenarios = require('../../common_scenarios/product');

let productData = {
  name: 'Att2',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'att',
  attribute: {
    name: 'attribute',
    variation_quantity: '10'
  }
};

let attributeData = {
  name: 'attribute',
  public_name: 'attribute',
  type: 'radio',
  values: {
    1: '10',
    2: '20',
    3: '30'
  }
};

require('../../high/04_attribute/1_create_edit_delete_attribute_value');

scenario('Delete "Attribute" with bulk actions', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');

  common_attribute.createAttribute(attributeData);
  common_scenarios.createProduct(AddProductPage, productData);
  common_attribute.attributeBulkActions(attributeData, 'delete');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  common_attribute.checkDeletedAttributeInFO(productData.name);

}, 'attribute_and_feature', true);
