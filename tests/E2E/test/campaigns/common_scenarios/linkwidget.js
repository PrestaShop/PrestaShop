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
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {LinkWidget} = require('../../selectors/BO/design/link_widget');
const {Menu} = require('../../selectors/BO/menu');
let promise = Promise.resolve();
module.exports = {
  createWidget(name, hook) {
    scenario('Create new "Link Widget" block ', client => {
      test('should go to "Design - Link Widget" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.link_widget_submenu));
      test('should click on "New block" button', () => client.waitForExistAndClick(LinkWidget.new_block_button));
      test('should set the "Name of the link block"', () => client.waitAndSetValue(LinkWidget.name_of_the_link_block_input, name + " " + date_time));
      test('should choose the hook "displayFooter"', () => client.waitAndSelectByVisibleText(LinkWidget.hook_select, hook));
      test('should select All the "content pages"', () => client.waitForExistAndClick(LinkWidget.select_all_content_page));
      test('should select All the "product pages"', () => client.waitForExistAndClick(LinkWidget.select_all_product_page));
      test('should select All the "static content"', () => client.waitForExistAndClick(LinkWidget.select_all_static_content));
      test('should set "Name" input', () => client.waitAndSetValue(LinkWidget.first_custom_content_name_input, 'Custom Name ' + date_time));
      test('should set "URL" input', () => client.waitAndSetValue(LinkWidget.first_custom_content_url_input, 'Custom Url ' + date_time));
      test('should click on "Add" button', () => client.waitForExistAndClick(LinkWidget.add_custom_content_button));
      test('should check that a new custom content bloc is added successfully', () => {
        return promise
          .then(() => client.waitForExist(LinkWidget.second_custom_content_name_input))
          .then(() => client.waitForExist(LinkWidget.second_custom_content_url_input));
      });
      test('should click on "save" button', () => client.waitForExistAndClick(LinkWidget.save_button));
      test('should verify the redirection to the link widget page', () => client.checkTextValue(LinkWidget.link_widget_configuration_bloc, 'LINK BLOCK CONFIGURATION', 'contain'));
      test('should refresh the page', () => client.refresh());
      test('should verify that the new block is displayed', () => client.checkTextValue(LinkWidget.last_widget_name_block.replace('%HOOK', hook), name + " " + date_time));
    }, 'common_client');
  },
  dragAndDropHookBO(name) {
    scenario('Change the Widget position ', client => {
      test('should change the position of the created block', () => client.dragAndDrop(LinkWidget.last_widget_drag_in_displayFooter_block.replace('%HOOK', name), LinkWidget.first_widget_drag_in_displayFooter_block.replace('%HOOK', name)));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.green_validation_notice));
      test('should refresh the page', () => client.refresh());
      test('should check that the new position is saved successfully', () => client.checkTextValue(LinkWidget.second_widget_in_displayFooter_block.replace('%HOOK', name), name + " " + date_time));
    }, 'common_client');
  }
};
