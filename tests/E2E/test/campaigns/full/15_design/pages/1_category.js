const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./pages');

let pageCategoryData = {
  name: 'Category',
  parent_category: 'demo',
  description: 'Category description',
  meta_title: 'Category meta title',
  meta_description: 'Category meta description',
  meta_keywords: 'Category meta keywords',
};

scenario('Create, edit, delete and delete with bulk actions page category', client => {

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  common_scenarios.createPageCategory(pageCategoryData);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');

}, 'common_client', true);