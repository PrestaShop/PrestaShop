const {Files} = require('../../../selectors/BO/catalogpage/files');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

module.exports = {
  createFile: function (filename, description, file) {
    scenario('Create a new "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should click on "Add new file" button', () => client.waitForExistAndClick(Files.add_new_file_button));
      test('should set the "Filenme" input', () => client.waitAndSetValue(Files.filename_input, filename));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Files.description_textarea, description));
      test('should upload the picture', () => client.uploadPicture(file, Files.file, 'file'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Files.save_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Files.success_alert));
    }, 'common_client');
  },
  editFile: function (filename, updated_filename, updated_description, updated_file) {
    scenario('Edit the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created file', () => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Files.edit_button));
      test('should set the "Filenme" input', () => client.waitAndSetValue(Files.filename_input, updated_filename));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Files.description_textarea, updated_description));
      test('should upload the picture', () => client.uploadPicture(updated_file, Files.file, 'file'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Files.save_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Files.success_alert));
    }, 'common_client');
  },
  checkFile: function (filename, description) {
    scenario('Check the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created file', () => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Files.edit_button));
      test('should check that the "Filenme" value is equal to "' + filename + '"', () => client.checkAttributeValue(Files.filename_input, 'value', filename));
      test('should check that the "Description" value is equal to "' + description + '"', () => client.checkAttributeValue(Files.description_textarea, 'value', description));
    }, 'common_client');
  },
  deleteFile: function (filename) {
    scenario('Delete the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created files', () => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      test('should delete the file', () => {
        return promise
          .then(() => client.waitForExistAndClick(Files.dropdown_button))
          .then(() => client.waitForExistAndClick(Files.delete_button))
          .then(() => client.alertAccept())
      });
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Files.success_alert));
    }, 'common_client');
  },
  deleteFileWithBulkAction: function (filename) {
    scenario('Delete the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created files', () => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      test('should delete the file with bulk action', () => {
        return promise
          .then(() => client.waitForExistAndClick(Files.bulk_action_button))
          .then(() => client.waitForExistAndClick(Files.action_group_button.replace('%ID', 1)))
          .then(() => client.waitForExistAndClick(Files.bulk_action_button))
          .then(() => client.waitForExistAndClick(Files.action_group_button.replace('%ID', 3)))
          .then(() => client.alertAccept())
      });
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(Files.success_alert));
    }, 'common_client');
  },
};