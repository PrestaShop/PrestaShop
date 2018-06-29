const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Pages} = require('../../../../selectors/BO/design/pages');
const {Menu} = require('../../../../selectors/BO/menu.js');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const common_scenarios = require('../../../common_scenarios/pages');

let promise = Promise.resolve();

let categoryDataWithoutSubCategory = {
  name: 'PageCategory',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords'
};

let pageData = {
  page_category: 'Home',
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

let pageWithCategory = {
  meta_title: 'page2',
  meta_description: 'page meta description',
  meta_keyword: ["keyword", "page"],
  page_content: 'page content'
};

scenario('Create, edit, delete "CMS page"', () => {

  scenario('Login in the Back Office and go to "Design > Pages" page', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
  }, 'design');

  scenario('Create CMS page then check it in the Back Office and the Front Office', client => {
    common_scenarios.createAndPreviewPage(pageData, "", 1);
    common_scenarios.checkPageBO(pageData.meta_title);
  }, 'design');

  scenario('Create CMS category page and check it in the Back office and the front office', client => {
    common_scenarios.createCategory(categoryDataWithoutSubCategory);
    scenario('Search for the created category CMS page in category', client => {
      test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Categories" table', () => {
        return promise
          .then(() => client.isVisible(Pages.Category.name_filter))
          .then(() => client.search(Pages.Category.name_filter, categoryDataWithoutSubCategory.name + date_time));
      });
      test('should click on "View" button', () => client.waitForExistAndClick(Pages.Category.view_button));
    }, 'design');
    common_scenarios.createAndPreviewPage(pageWithCategory, categoryDataWithoutSubCategory.name + date_time, 2);
    common_scenarios.checkCategoryBO(categoryDataWithoutSubCategory);
    scenario('Reset page filter and go to "Design > Pages" page', client => {
      test('should click on "Reset" button', () => client.waitForExistAndClick(Pages.Page.reset_button));
      test('should click on the created category "View" button', () => client.waitForExistAndClick(Pages.Category.view_button));
    }, 'design');
    common_scenarios.checkPageBO(pageWithCategory.meta_title);
  }, 'design');

  scenario('Edit the created CMS page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, pageData.meta_title + date_time));
    });
    test('should click on "Edit" button', () => client.waitForExistAndClick(Pages.Page.edit_button));
    common_scenarios.editPage(pageData, newPageData, "", 3);
  }, 'design');

  scenario('Edit the created CMS page in category', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should check the existence of the CMS category', () => {
      return promise
        .then(() => client.isVisible(Pages.Category.name_filter))
        .then(() => client.search(Pages.Category.name_filter, categoryDataWithoutSubCategory.name + date_time));
    });
    test('should click on "Reset" button', () => client.waitForExistAndClick(Pages.Page.reset_button));
    test('should click on the created category "View" button', () => client.waitForExistAndClick(Pages.Category.view_button));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, pageWithCategory.meta_title + date_time));
    });
    test('should click on "Edit" button', () => client.waitForExistAndClick(Pages.Page.edit_button));
    common_scenarios.editPage(pageWithCategory, newPageData, categoryDataWithoutSubCategory.name + date_time, 4);
  }, 'design');

  scenario('Delete the CMS page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, newPageData.meta_title + date_time));
    });
    common_scenarios.deletePage();
    scenario('Check the review page', client => {
      test('should go to the review page in the Front Office', () => client.switchWindow(1));
      test('should check that "page-not-found" appear', () => client.checkTextValue(AccessPageFO.not_found_erreur_message, 'The page you are looking for was not found.'));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'design');
  }, 'design');

  scenario('Delete the CMS Category page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
    test('should check the existence of the CMS category', () => {
      return promise
        .then(() => client.isVisible(Pages.Category.name_filter))
        .then(() => client.search(Pages.Category.name_filter, categoryDataWithoutSubCategory.name + date_time));
    });
    test('should click on "Reset" button', () => client.waitForExistAndClick(Pages.Page.reset_button));
    test('should click on the created category "View" button', () => client.waitForExistAndClick(Pages.Category.view_button));
    test('should search for the page in "pages list"', () => {
      return promise
        .then(() => client.isVisible(Pages.Page.title_filter_input))
        .then(() => client.search(Pages.Page.title_filter_input, pageWithCategory.meta_title + date_time));
    });
    common_scenarios.deletePage();
    scenario('Check the review page', client => {
      test('should go to the review page in the Front Office', () => client.switchWindow(2));
      test('should check that "page-not-found" appear', () => client.isNotExisting(AccessPageFO.review_page_link.replace('%PAGENAME', newPageData.meta_title)));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'design');
  }, 'design');

  common_scenarios.createAndPreviewPage(pageData, "", 5);
  common_scenarios.createAndPreviewPage(pageData, "", 6);
  common_scenarios.PageBulkActions(pageData.meta_title, 'Enable');
  common_scenarios.PageBulkActions(pageData.meta_title, 'Disable');
  common_scenarios.PageBulkActions(pageData.meta_title, 'Delete');

  scenario('logout successfully from the Back Office', client => {
    test('should go to the Back Office', () => client.switchWindow(0));
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design', true);