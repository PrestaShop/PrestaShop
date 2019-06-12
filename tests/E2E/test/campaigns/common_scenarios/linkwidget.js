const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {LinkWidget} = require('../../selectors/BO/design/link_widget');
const {Menu} = require('../../selectors/BO/menu');
let promise = Promise.resolve();
module.exports = {
  createWidget(name, hook) {
    scenario('Create new "Link Widget" block ', client => {
      test('should go to "Design - Link Widget" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.link_widget_submenu));
      test('should click on "New block" button', () => client.waitForExistAndClick(LinkWidget.new_block_button));
      test('should set the "Name of the link block in english"', () => client.waitAndSetValue(LinkWidget.name_of_the_link_block_input.replace('%lang','1'), name + " " + date_time));
      test('should switch input language to french', () => {
        return promise
          .then(() => client.waitForExistAndClick(LinkWidget.name_of_the_link_block_lang_button))
          .then(() => client.waitForVisibleAndClick(LinkWidget.name_of_the_link_block_lang_span.replace('%lang','fr')));
      });
      test('should set the "Name of the link block in french', () => client.waitAndSetValue(LinkWidget.name_of_the_link_block_input.replace('%lang','2'), name + " " + date_time));
      test('should choose the hook "displayFooter"', () => client.waitAndSelectByVisibleText(LinkWidget.hook_select, hook));
      test('should select All the "content pages"', async () => await client.selectAllOptionsLinkWidget(LinkWidget.select_all_content_page));
      test('should select All the "product pages"', async () => await client.selectAllOptionsLinkWidget(LinkWidget.select_all_product_page));
      test('should select All the "static content"', async () => await client.selectAllOptionsLinkWidget(LinkWidget.select_all_static_content));
      test('should set "Name" input in french', () => client.waitAndSetValue(LinkWidget.first_custom_content_name_input.replace('%POS','1').replace('%lang','2'), 'Custom Name ' + date_time));
      test('should set "URL" input in french', () => client.waitAndSetValue(LinkWidget.first_custom_content_url_input.replace('%POS','1').replace('%lang','2'), 'Custom Url ' + date_time));
      test('should switch input language to english', () => {
        return promise
          .then(() => client.waitForExistAndClick(LinkWidget.custom_content_lang_button.replace('%POS','1')))
          .then(() => client.waitForVisibleAndClick(LinkWidget.custom_content_lang_span.replace('%lang','en').replace('%POS','1')));
      });
      test('should set "Name" input in english', () => client.waitAndSetValue(LinkWidget.first_custom_content_name_input.replace('%POS','1').replace('%lang','1'), 'Custom Name ' + date_time));
      test('should set "URL" input in english', () => client.waitAndSetValue(LinkWidget.first_custom_content_url_input.replace('%POS','1').replace('%lang','1'), 'Custom Url ' + date_time));
      test('should click on "Add" button', () => client.scrollWaitForExistAndClick(LinkWidget.add_custom_content_button));
      test('should check that a new custom content bloc is added successfully', () => {
        return promise
          .then(() => client.waitForExist(LinkWidget.first_custom_content_name_input.replace('%POS','2').replace('%lang','1')))
          .then(() => client.waitForExist(LinkWidget.first_custom_content_url_input.replace('%POS','2').replace('%lang','1')));
      });
      test('should click on "save" button', () => client.scrollWaitForExistAndClick(LinkWidget.save_button));
      test('should refresh the page', () => client.refresh());
      test('should verify that the new block is displayed', () => client.checkTextValue(LinkWidget.last_widget_name_block.replace('%HOOK', hook), name + " " + date_time));
    }, 'common_client');
  },
  dragAndDropHookBO(name) {
    scenario('Change the Widget position ', client => {
      test('should change the position of the created block', () => client.dragAndDrop(LinkWidget.last_widget_drag_in_displayFooter_block.replace('%HOOK', name), LinkWidget.first_widget_drag_in_displayFooter_block.replace('%HOOK', name)));
      test('should check that the success alert message is well displayed', () => client.checkTextValue(LinkWidget.success_panel, 'Successful update.'));
      test('should refresh the page', () => client.refresh());
      test('should check that the new position is saved successfully', () => client.checkTextValue(LinkWidget.second_widget_in_displayFooter_block.replace('%HOOK', name), name + " " + date_time));
    }, 'common_client');
  }
};
