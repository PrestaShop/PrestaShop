/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const common_scenarios = require('../../../common_scenarios/pages');

let categoryDataWithoutSubCategory = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords'
};

let categoryDataWithSubCategory = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords',
  sub_category: {
    name: 'subCategory',
    description: 'sub category description',
    meta_title: 'sub category meta title',
    meta_description: 'sub category meta description',
    meta_keywords: 'sub category meta keywords'
  }
};

let newCategoryData = {
  name: 'editCategory',
  parent_category: '1',
  description: 'new category description',
  meta_title: 'new category meta title',
  meta_description: 'new category meta description',
  meta_keywords: 'new category meta keywords',
  sub_category: {
    name: 'subCategory',
    parent_category: '1',
    description: 'new sub category description',
    meta_title: 'new sub category meta title',
    meta_description: 'new sub category meta description',
    meta_keywords: 'new sub category meta keywords'
  }
};

let pageData = {
  page_category: 'editCategory',
  meta_title: 'page1',
  meta_description: 'page meta description',
  meta_keyword: ["keyword", "page"],
  page_content: 'page content'
};

scenario('Create, edit and delete "CATEGORIES"', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'design');

  common_scenarios.createCategory(categoryDataWithSubCategory);
  common_scenarios.checkCategoryBO(categoryDataWithSubCategory);
  common_scenarios.editCategory(categoryDataWithSubCategory, newCategoryData);

  scenario('go to "Design > Pages" page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
  }, 'design');

  common_scenarios.createAndPreviewPage(pageData);
  common_scenarios.deleteCategory(newCategoryData.name);
  common_scenarios.deleteCategory(newCategoryData.sub_category.name);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name, "disable");
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name, "enable");
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design', true);