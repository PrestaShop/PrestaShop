const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonProduct = require('../../common_scenarios/product');
const commonFeature = require('../../common_scenarios/feature');

let promise = Promise.resolve();

let productData = {
  name: 'Feat',
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

scenario('Create "Feature"', () => {
  /* Create feature */
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');

  commonFeature.createFeature(featureData, featureData.values.length);
  commonProduct.createProduct(AddProductPage, productData);

  /* Check the created feature */
  scenario('Login in the Front Office', client => {
    test('should click on "View my shop" button', () => {
      return promise
        .then(() => client.pause(4000))
        .then(() => client.waitForVisibleAndClick(AccessPageBO.shopname));
    });
    test('should switch to the new window', () => client.switchWindow(1));
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    test('should set the language of shop to "English"', () => client.changeLanguage());
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData.name, featureData);

  /* Update the created feature */
  scenario('Go back to the Back Office', client => {
    test('should switch to the Back Office', () => client.switchWindow(0));
    test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
  }, 'attribute_and_feature');
  commonFeature.updateFeature(featureData, featureData.values.length);

  /* Check the updated feature */
  scenario('Go back to the Front Office page and check the modification', client => {
    test('should switch to the Front office', () => client.switchWindow(1));
    test('should set the language of shop to "English"', () => client.changeLanguage());
  }, 'attribute_and_feature');
  commonFeature.checkFeatureInFO(productData.name, featureData);

   /* Delete feature value*/
   scenario('Go back to the Back Office', client => {
     test('should switch to the back office', () => client.switchWindow(0));
     test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
   }, 'attribute_and_feature');
   commonFeature.deleteValue(featureData);

   /* Check the deleted feature */
 scenario('Go back to the Front Office', client => {
   test('should switch to the Front office', () => client.switchWindow(1));
   test('should set the language of shop to "English"', () => client.changeLanguage());
   }, 'attribute_and_feature');
   commonFeature.checkDeletedValueInFO(productData.name, featureData);

  /* Delete feature */
  scenario('Go back to the Back Office', client => {
    test('should switch to the Back office', () => client.switchWindow(0));
  }, 'attribute_and_feature');
  commonFeature.deleteFeature(featureData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');

  /* Check the deleted feature */
  scenario('Go back to the Front Office', client => {
    test('should switch to the Front office', () => client.switchWindow(1));
    test('should set the language of shop to "English"', () => client.changeLanguage());
  }, 'attribute_and_feature');
  commonFeature.checkDeletedFeatureInFO(productData.name);
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');

}, 'attribute_and_feature', true);
