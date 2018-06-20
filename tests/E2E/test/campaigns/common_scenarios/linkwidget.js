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
      test('should fill the fields "Name"', () => client.waitAndSetValue(LinkWidget.first_custom_content_name_input, 'Custom Name ' + date_time));
      test('should fill the fields "Url"', () => client.waitAndSetValue(LinkWidget.first_custom_content_url_input, 'Custom Url ' + date_time));
      test('should click on "Add" button', () => client.waitForExistAndClick(LinkWidget.add_custom_content_button));
      test('should check that a new custom content bloc added', () => {
        return promise
          .then(() => client.waitForExist(LinkWidget.second_custom_content_name_input))
          .then(() => client.waitForExist(LinkWidget.second_custom_content_url_input));
      });
      test('should click on "save" button', () => client.waitForExistAndClick(LinkWidget.save_button));
      test('should verify the redirection to the link widget page', () => client.checkTextValue(LinkWidget.link_widget_configuration_bloc, 'LINK BLOCK CONFIGURATION', 'contain'));
      test('should refresh the page', () => client.refresh());
      test('should verify if the added block is displayed', () => client.checkTextValue(LinkWidget.last_widget_name_in_displayFooter_block.replace('%HOOK', " " + hook), name + " " + date_time));
    }, 'common_client');
  },
  dragAndDropHookBO(name) {
    scenario('Change the Widget position ', client => {
      test('should change the position of the created block', () => client.dragAndDrop(LinkWidget.last_widget_drag_in_displayFooter_block.replace('%HOOK', name), LinkWidget.first_widget_drag_in_displayFooter_block.replace('%HOOK', name)));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.green_validation_notice));
      test('should refresh the page', () => client.refresh());
      test('should check if the position have been saved', () => client.checkTextValue(LinkWidget.second_widget_in_displayFooter_block.replace('%HOOK', name), name + " " + date_time));
    }, 'common_client');
  }
};
