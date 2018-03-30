const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Pages} = require('../../../../selectors/BO/design/pages');
const {Menu} = require('../../../../selectors/BO/menu.js');
const common_scenarios = require('../../../common_scenarios/pages');

let promise = Promise.resolve();

let pageData = {
  page_category: '1',
  meta_title: 'page1',
  meta_description: 'page meta description',
  meta_keyword: ["keyword", "page"],
  page_content: 'page content'
};

let newPageData = {
  meta_title: 'editpage',
  meta_description: 'edit page meta description',
  meta_keyword: ["edit"],
  page_content: 'edit page content'
};

let categoryData = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords'
};

let pageWithCategory = {
  meta_title: 'page2',
  meta_description: 'page meta description',
  meta_keyword: ["keyword", "page"],
  page_content: 'page content'
};

scenario('Create, edit, delete and delete with bulk actions CMS page', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'design');
  common_scenarios.createPage(pageData);
  common_scenarios.checkPageBO(pageData.meta_title);
  common_scenarios.checkPageFO(pageData);
  scenario('Edit the created CMS page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, pageData.meta_title + date_time));
    });
    test('should click on "Edit" button', () => client.waitForExistAndClick(Pages.Page.edit_button));
    common_scenarios.editPage(pageData, newPageData);
  }, 'design');
  common_scenarios.checkPageFO(newPageData);
  scenario('Search for the CMS page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, newPageData.meta_title + date_time));
    });
    common_scenarios.deletePage();
  }, 'design');
  common_scenarios.createCategory(categoryData);
  scenario('Get the created cms category ID', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should get the parent category ID', () => {
      return promise
        .then(() => client.isVisible(Pages.Category.name_filter))
        .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time))
        .then(() => client.getCategoryID(Pages.Category.search_name_result, 2));
    });
  }, 'design');
  common_scenarios.createPage(pageWithCategory);
  scenario('Check the existence of the cms page in the category list', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should click on "Reset" button', () => client.waitForExistAndClick(Pages.Page.reset_button));
    test('should go to the created cms category pages list', () => {
      return promise
        .then(() => client.isVisible(Pages.Category.name_filter))
        .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time))
        .then(() => client.waitForExistAndClick(Pages.Category.view_button));
    });
    test('should check the existence of the created page', () => client.checkTextValue(Pages.Page.search_title_result.replace("%ID", 3), pageWithCategory.meta_title + date_time));
  }, 'design');
  scenario('Edit the CMS page', client => {
    test('should click on "Edit" button', () => client.waitForExistAndClick(Pages.Page.edit_button));
    common_scenarios.editPage(pageWithCategory, newPageData);
  }, 'design');
  common_scenarios.checkPageFO(newPageData);
  common_scenarios.deletePage();
  common_scenarios.createPage(pageData);
  common_scenarios.createPage(pageData);
  common_scenarios.PageBulkActions(pageData.meta_title, 'enable');
  common_scenarios.PageBulkActions(pageData.meta_title, 'disable');
  common_scenarios.PageBulkActions(pageData.meta_title);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design', true);