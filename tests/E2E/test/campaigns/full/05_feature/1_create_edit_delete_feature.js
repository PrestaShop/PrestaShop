const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-42"][Name="Edit Feature"]
 * [id="PS-43"][Name="Delete Feature"]
 **/

const commonFeature = require('../../common_scenarios/feature');
const commonProduct = require('../../common_scenarios/product');
const welcomeScenarios = require('../../common_scenarios/welcome');

let productData = {
  name: 'Feat2',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'feat',
  feature: [
    {
      name: 'Feature',
      value: 'Value 1'
    }, {
      name: 'Feature',
      value: 'Value 2'
    }, {
      name: 'Feature',
      value: 'Value 3'
    }
  ]
};

let featureData = {
  name: 'Feature',
  values: ['Value 1', 'Value 2', 'Value 3']
};

let promise = Promise.resolve();

let productData2 = {
  name: 'Feat',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'feat',
  feature: [
    {
      name: 'Feature1',
      value: 'Value 1'
    }, {
      name: 'Feature1',
      value: 'Value 2'
    }, {
      name: 'Feature1',
      value: 'Value 3'
    }
  ]
};

let featureData2 = {
  name: 'Feature1',
  values: ['Value 1', 'Value 2', 'Value 3']
};

scenario('Create, edit and delete "Feature"', () => {
  /* Create feature */
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonFeature.createFeature(featureData2);
  commonProduct.createProduct(AddProductPage, productData2);
  /* Check the created feature */
  scenario('Login in the Front Office', client => {
    test('should click on "View my shop" button', () => {
      return promise
          .then(() => client.pause(4000))
          .then(() => client.waitForVisibleAndClick(AccessPageBO.shopname));
    });
    test('should switch to the new window', () => client.switchWindow(1));
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData2.name, featureData2);
  /* Update the created feature */
  scenario('Go back to the Back Office', client => {
    test('should switch to the Back Office', () => client.switchWindow(0));
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.updateFeature(featureData2);
  /* Check the updated feature */
  scenario('Go back to the Front Office page and check the modification', client => {
    test('should switch to the Front office', () => client.switchWindow(1));
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData2.name, featureData2);
  /* Delete feature value */
  scenario('Go back to the Back Office', client => {
    test('should switch to the back office', () => client.switchWindow(0));
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.deleteValue(featureData2);
  /* Check the deleted feature */
  scenario('Go back to the Front Office', client => {
    test('should switch to the Front office', () => client.switchWindow(1));
  }, 'attribute_and_feature');
  commonFeature.checkDeletedValueInFO(productData2.name, featureData2);
  /* Delete feature */
  scenario('Go back to the Back Office', client => {
    test('should switch to the Back office', () => client.switchWindow(0));
  }, 'attribute_and_feature');
  commonFeature.deleteFeature(featureData2);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
  /* Check the deleted feature */
  scenario('Go back to the Front Office', client => {
    test('should switch to the Front office', () => client.switchWindow(1));
  }, 'attribute_and_feature');
  commonFeature.checkDeletedFeatureInFO(productData2.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Delete "Feature" with bulk actions', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');

  welcomeScenarios.findAndCloseWelcomeModal();
  commonFeature.createFeature(featureData);
  commonProduct.createProduct(AddProductPage, productData);
  commonFeature.featureBulkActions(featureData, 'delete');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonFeature.checkDeletedFeatureInFO(productData.name);

}, 'attribute_and_feature', true);
