const {ShopParameters, Contact} = require('../../selectors/BO/shopParameters/shop_parameters');
const {ContactUsPageFO} = require('../../selectors/FO/contact_us_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {CustomerServicePage} = require('../../selectors/BO/customerService/customer_service');
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
      test('should click on "Add new contact" button', () => client.waitForExistAndClick(Contact.Contacts.add_new_contact_button));
      test('should set the "Title" input', () => client.waitAndSetValue(Contact.Contacts.title_input, contactData.title + date_time));
      test('should set the "Email address" input', () => client.waitAndSetValue(Contact.Contacts.email_address_input, contactData.email));
      test('should enable the "Save messages"', () => client.waitForExistAndClick(Contact.Contacts.save_messages_button));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Contact.Contacts.description_textarea, contactData.description));
      test('should click on "Save" button', () => client.waitForExistAndClick(Contact.Contacts.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  editContact: function (contactData) {
    scenario('Edit the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created tax rule', () => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Contact.Contacts.edit_button))
          .then(() => client.editObjectData(contactData));
      });
      test('should set the "Title" input', () => client.waitAndSetValue(Contact.Contacts.title_input, contactData.title + date_time));
      test('should set the "Email address" input', () => client.waitAndSetValue(Contact.Contacts.email_address_input, contactData.email));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Contact.Contacts.description_textarea, contactData.description));
      test('should click on "Save" button', () => client.waitForExistAndClick(Contact.Contacts.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkContactBO: function (contactData) {
    scenario('Check the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Contact.Contacts.edit_button))
          .then(() => client.getParamFromURL('id_contact', 1000));
      });
      test('should check the contact\'s "Title"', () => client.checkAttributeValue(Contact.Contacts.title_input, 'value', contactData.title + date_time));
      test('should check the contact\'s "Email address"', () => client.checkAttributeValue(Contact.Contacts.email_address_input, 'value', contactData.email));
      test('should check the contact\'s "Description"', () => client.checkAttributeValue(Contact.Contacts.description_textarea, 'value', contactData.description));
    }, 'common_client');
  },
  checkContactFO: function (contactData, isDeleted = false) {
    scenario('Check the created "Contact"', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should click on "Contact us" button', () => client.waitForExistAndClick(ContactUsPageFO.contact_us_button));
      test('should click on "Subject" select', () => client.waitForExistAndClick(ContactUsPageFO.subject_select));
      if (isDeleted) {
        test('should check that the contact is well deleted', () => client.isNotExisting(ContactUsPageFO.subject_select_option.replace('%V', param['id_contact'])));
      } else {
        test('should check that the contact is well updated', () => client.checkTextValue(ContactUsPageFO.subject_select_option.replace('%V', param['id_contact']), contactData.title + date_time));
      }
    }, 'common_client');
  },
  configureContactFormModule: function () {
    scenario('Configure the "Contact form" module', client => {
      test('should go to "Module" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
      test('should set the name of the module in the search input', () => client.waitAndSetValue(ModulePage.module_selection_input, 'contactform'));
      test('should click on "Search" button', () => client.waitForExistAndClick(ModulePage.selection_search_button));
      test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.configure_link.replace('%moduleTechName', 'contactform')));
      test('should switch the "Send confirmation email" to "YES"', () => client.waitForExistAndClick(ModulePage.ContactFormPage.send_confirmation_email_button.replace('%S', 'on')));
      test('should switch the "Receive customers messages by email" to "YES"', () => client.waitForExistAndClick(ModulePage.ContactFormPage.receive_customers_messages_label.replace('%S', 'on')));
      test('should click on "Save" button', () => client.waitForExistAndClick(ModulePage.ContactFormPage.save_button));
      //Related issue: 9646
      test('should verify the appearance of the green validation', () => client.checkTextValue(ModulePage.success_msg, '×\nSuccessful update.'));
    }, 'common_client');
  },
  /****Example of contact data ****
   * var messageData = {
   *  email: 'email',
   *  attachment: 'file',
   *  message: 'message',
   * };
   */
  sendMessageFO: function (messageData) {
    scenario('Check the created "Contact"', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should click on "Contact us" button', () => client.waitForExistAndClick(ContactUsPageFO.contact_us_button));
      test('should choose the created contact from the dropdown list', () => client.waitAndSelectByValue(ContactUsPageFO.subject_select, param['id_contact']));
      test('should set the contact\'s "Email address"', () => client.waitAndSetValue(ContactUsPageFO.email_address_input, messageData.email));
      test('should upload an attachment', () => client.uploadPicture(messageData.attachment, ContactUsPageFO.attachment_file, 'filestyle'));
      test('should set the contact\'s "Description"', () => client.waitAndSetValue(ContactUsPageFO.message_textarea, messageData.message));
      test('should click on "Send" button', () => client.waitForExistAndClick(ContactUsPageFO.send_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(ContactUsPageFO.success_panel, 'Your message has been successfully sent to our team.'));
    }, 'common_client');
  },
  checkCustomerService: function (contactData, messageData, isUpdated = false, isDeleted = false) {
    scenario('Check the created "Contact"', client => {
      test('should go to "Customer Service" page', () => client.goToSubtabMenuPage(Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.customer_service_submenu));
      test('should search for the created contact', () => {
        return promise
          .then(() => client.isVisible(CustomerServicePage.email_filter_input))
          .then(() => client.search(CustomerServicePage.email_filter_input, messageData.email));
      });
      if (isDeleted) {
        test('should click on "Dropdown" button', () => client.waitForExistAndClick(CustomerServicePage.dropdown_button));
        test('should click on "Delete" action', () => client.waitForExistAndClick(CustomerServicePage.delete_button));
        test('should accept the confirmation alert', () => client.alertAccept());
        test('should verify the appearance of the green validation', () => client.checkTextValue(CustomerServicePage.success_panel, '×\nSuccessful deletion.'));
      } else {
        test('should click on "View" button', () => client.waitForExistAndClick(CustomerServicePage.view_button));
        if (isUpdated) {
          test('should check the message\'s "Type"', () => client.checkTextValue(CustomerServicePage.email_receive_text, contactData.title + date_time));
        } else {
          test('should check the message\'s "Email"', () => client.checkTextValue(CustomerServicePage.email_sender_text, messageData.email));
          test('should check the message\'s "Type"', () => client.checkTextValue(CustomerServicePage.email_receive_text, contactData.title + date_time));
          test('should check the message\'s "Message"', () => client.checkTextValue(CustomerServicePage.message_text, messageData.message));
        }
      }
    }, 'common_client');
  },
  checkTitleCustomerService: function (contactData, messageData) {
    scenario('Check the customer service\'s title', client => {
      test('should go to "Customer Service" page', () => client.goToSubtabMenuPage(Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.customer_service_submenu));
      test('should search for the created contact', () => {
        return promise
          .then(() => client.isVisible(CustomerServicePage.email_filter_input))
          .then(() => client.search(CustomerServicePage.email_filter_input, messageData.email));
      });
      test('should click on "View" button', () => client.waitForExistAndClick(CustomerServicePage.view_button));
      test('should check that the message\'s "Type" is well updated', () => client.checkTextValue(CustomerServicePage.email_receive_text, contactData.title + date_time));
    }, 'common_client');
  },
  deleteContact: function (contactData) {
    scenario('Delete the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, contactData.title + date_time));
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(Contact.Contacts.dropdown_button));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Contact.Contacts.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_panel, '×\nSuccessful deletion.'));
      test('should check that the contact is well deleted', () => {
        return promise
          .then(() => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, contactData.title + date_time))
          .then(() => client.checkTextValue(Contact.Contacts.empty_list, 'No records found', 'contain'));
      });
    }, 'common_client');
  },
  deleteContactWithBulkAction: function (title) {
    scenario('Delete the created "Contact"', client => {
      test('should go to "Shop Parameters > Contact" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu));
      test('should search for the created contact', () => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, title + date_time));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Contact.Contacts.bulk_action_button));
      test('should click on "Unselect all" action', () => client.waitForExistAndClick(Contact.Contacts.bulk_actions_unselect_all_button));
      test('should check that the checkbox is well unselected', () => client.isNotSelected(Contact.Contacts.checkbox_element, 10000));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Contact.Contacts.bulk_action_button));
      test('should click on "Select all" action', () => client.waitForExistAndClick(Contact.Contacts.bulk_actions_select_all_button));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Contact.Contacts.bulk_action_button));
      test('should click on "Delete" action', () => client.waitForExistAndClick(Contact.Contacts.bulk_actions_delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(ShopParameters.success_panel, '×\nThe selection has been successfully deleted.'));
      test('should check that the selected contact is well deleted', () => {
        return promise
          .then(() => client.searchByValue(Contact.Contacts.filter_title_input, Contact.Contacts.filter_search_button, title + date_time))
          .then(() => client.checkTextValue(Contact.Contacts.empty_list, 'No records found', 'contain'));
      });
    }, 'common_client');
  }
};
