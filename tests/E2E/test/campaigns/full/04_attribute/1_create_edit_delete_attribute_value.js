/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-39"][Name="Edit Attrib"]
 * [id="PS-40"][Name="Delete Attrib"]
 **/

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

let productData2 = {
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

let attributeData2 = {
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
  commonAttribute.createAttribute(attributeData2);
  commonScenarios.createProduct(AddProductPage, productData2);

  /* Check the created attribute */
  scenario('Go to the Front Office', client => {
    test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData2.name, attributeData2);

  /* Update the created attribute */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.updateAttribute(attributeData2);

  /* Check the updated attribute */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData2.name, attributeData2);

  /* Delete attribute value */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.deleteAttributeValue(attributeData2);

  /* Check the deleted attribute value */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkAttributeInFO(productData2.name, attributeData2);

  /* Delete the created attribute */
  scenario('Go back to the Back Office', client => {
    test('should login successfully in the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonAttribute.deleteAttribute(attributeData2);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  /* Check the deleted attribute */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonAttribute.checkDeletedAttributeInFO(productData2.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

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
