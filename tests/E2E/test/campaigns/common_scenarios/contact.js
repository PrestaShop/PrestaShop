const {ShopParameters} = require('../../selectors/BO/shopParameters/shop_parameters');
const {ShopParametersPage} = require('../../selectors/BO/shopParameters/index');
const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();

/****Example of contact data ****
 * var contactData = {
 *  title: 'contact_title',
 *  email: 'contact_email',
 *  description: 'description_of_the_contact',
 * };
 */

module.exports = {
  createContact: function (contactData) {
    scenario('Create a new "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should click on "Add new contact" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.add_new_contact_button));
      test('should set the "Title" input', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.title_input, contactData.title + date_time));
      test('should set the "Email address" input', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.email_address_input, contactData.email));
      test('should enable the "Save messages"', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.save_messages_button));
      test('should set the "Description" textarea', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.description_textarea, contactData.description));
      test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParametersPage.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  editContact: function (contactData) {
    scenario('Edit the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created tax rule', () => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(ShopParameters.Contact.Contacts.edit_button))
          .then(() => client.editObjectData(contactData))
      });
      test('should set the "Title" input', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.title_input, contactData.title + date_time));
      test('should set the "Email address" input', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.email_address_input, contactData.email));
      test('should set the "Description" textarea', () => client.waitAndSetValue(ShopParameters.Contact.Contacts.description_textarea, contactData.description));
      test('should click on "Save" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParametersPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkContact: function (contactData) {
    scenario('Check the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.edit_button));
      test('should check the contact\'s "Title"', () => client.checkAttributeValue(ShopParameters.Contact.Contacts.title_input, 'value', contactData.title + date_time));
      test('should check the contact\'s "Email address"', () => client.checkAttributeValue(ShopParameters.Contact.Contacts.email_address_input, 'value', contactData.email));
      test('should check the contact\'s "Description"', () => client.checkAttributeValue(ShopParameters.Contact.Contacts.description_textarea, 'value', contactData.description));
    }, 'common_client');
  },
  deleteContact: function (contactData) {
    scenario('Delete the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.dropdown_button));
      test('should click on "Delete" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParametersPage.success_panel, '×\nSuccessful deletion.'));
      test('should check that the contact is well deleted', () => {
        return promise
          .then(() => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, contactData.title + date_time))
          .then(() => client.checkTextValue(ShopParameters.Contact.Contacts.empty_list, 'No records found', 'contain'))
      });
    }, 'common_client');
  },
  deleteContactWithBulkAction: function (title) {
    scenario('Delete the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, title + date_time));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.bulk_action_button));
      test('should click on "Select all" action', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.bulk_actions_select_all_button));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.bulk_action_button));
      test('should click on "Delete" action', () => client.waitForExistAndClick(ShopParameters.Contact.Contacts.bulk_actions_delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParametersPage.success_panel, '×\nThe selection has been successfully deleted.'));
      test('should check that the selected contact is well deleted', () => {
        return promise
          .then(() => client.searchByValue(ShopParameters.Contact.Contacts.filter_title_input, ShopParameters.Contact.Contacts.filter_search_button, title + date_time))
          .then(() => client.checkTextValue(ShopParameters.Contact.Contacts.empty_list, 'No records found', 'contain'))
      });
    }, 'common_client');
  }
};