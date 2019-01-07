const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonScenarios = require('../../common_scenarios/product');
const commonAttribute = require('../../common_scenarios/attribute');
const welcomeScenarios = require('../../common_scenarios/welcome');

let productData = {
  name: 'Att',
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

scenario('Create, edit and delete "Attribute"', () => {
  /* Create attribute */
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonAttribute.createAttribute(attributeData);
  commonScenarios.createProduct(AddProductPage, productData);

  /* Check the created attribute */
  scenario('Go to the Front Office', client => {
    test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData.name, attributeData);

  /* Update the created attribute */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.updateAttribute(attributeData);

  /* Check the updated attribute */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData.name, attributeData);

  /* Delete attribute value */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.deleteAttributeValue(attributeData);

  /* Check the deleted attribute value */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData.name, attributeData);

  /* Delete the created attribute */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.deleteAttribute(attributeData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  /* Check the deleted attribute */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkDeletedAttributeInFO(productData.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
