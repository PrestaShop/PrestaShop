const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonProduct = require('../../common_scenarios/product');
const commonFeature = require('../../common_scenarios/feature');

let productData = {
  name: 'Feat',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'feat',
  feature: {
    name: 'Feature',
    value: 'Feature Value'
  }
};

let featureData = {
  name: 'Feature',
  values: {
    1: 'Feature Value'
  }
};

scenario('Create "Feature"', () => {
  /* Create feature */
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.createFeature(featureData);
  commonProduct.createProduct(AddProductPage, productData);

  /* Check the created feature */
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData.name, featureData);

  /* Update the created feature */
  scenario('Go back to the Back Office', client => {
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.updateFeature(featureData);

  /* Check the updated feature */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData.name, featureData);

  /* Delete feature */
  scenario('Go back to the Back Office', client => {
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.deleteFeature(featureData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  /* Check the deleted feature */
  scenario('Go back to the Front Office', client => {
    test('should go back to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'attribute_and_feature');
  commonFeature.checkDeletedFeatureInFO(productData.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);