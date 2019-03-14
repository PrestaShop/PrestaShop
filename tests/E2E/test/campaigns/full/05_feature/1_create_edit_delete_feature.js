const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
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

require('../../high/05_feature/1_create_edit_delete_feature');

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
