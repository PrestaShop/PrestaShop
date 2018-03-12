const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../02_product/product');
const common_attribute = require('./attribute');


let productData = {
  name: 'Att',
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

scenario('Create, edit and delete "Attribute"', () => {
  /* Create attribute */
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  common_attribute.createAttribute(attributeData);
  common_scenarios.createProduct(AddProductPage, productData);

  /* Check the created attribute */
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  common_attribute.checkAttributeInFO(productData.name, attributeData);

  /* Update the created attribute */
  scenario('Login in the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  common_attribute.updateAttribute(attributeData);

  /* Check the updated attribute */
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  common_attribute.checkAttributeInFO(productData.name, attributeData);

  /* Delete attribute value */
  scenario('Login in the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  common_attribute.deleteAttributeValue(attributeData);

  /* Check the deleted attribute value */
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  common_attribute.checkAttributeInFO(productData.name, attributeData);

  /* Delete the created attribute */
  scenario('Login in the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  common_attribute.deleteAttribute(attributeData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  /* Check the deleted attribute */
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  common_attribute.checkDeletedAttributeInFO(productData.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
