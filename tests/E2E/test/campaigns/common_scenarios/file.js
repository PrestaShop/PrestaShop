const {Files} = require('../../selectors/BO/catalogpage/files');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {Menu} = require('../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
const {productPage} = require('../../selectors/FO/product_page');
let promise = Promise.resolve();

module.exports = {
  createFile: function (filename, description, file) {
    scenario('Create a new "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should click on "Add new file" button', () => client.waitForExistAndClick(Files.add_new_file_button));
      test('should set the "Filename" input', () => client.waitAndSetValue(Files.filename_input, filename));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Files.description_textarea, description));
      test('should upload the picture', () => client.uploadPicture(file, Files.file, 'file'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Files.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  editFile: function (filename, updatedFilename, updatedDescription, updatedFile) {
    scenario('Edit the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created file', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Files.edit_button));
      test('should set the "Filename" input', () => client.waitAndSetValue(Files.filename_input, updatedFilename));
      test('should set the "Description" textarea', () => client.waitAndSetValue(Files.description_textarea, updatedDescription));
      test('should upload the picture', () => client.uploadPicture(updatedFile, Files.file, 'file'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Files.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
    }, 'file');
  },
  checkFile: function (filename, description) {
    scenario('Check the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created file', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Files.edit_button));
      test('should check that the "Filename" value is equal to "' + filename + '"', () => client.checkAttributeValue(Files.filename_input, 'value', filename));
      test('should check that the "Description" value is equal to "' + description + '"', () => client.checkAttributeValue(Files.description_textarea, 'value', description));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
    }, 'file');
  },
  deleteFile: function (filename) {
    scenario('Delete the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button, 3000))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
      test('should search for the created files', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      });
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(Files.dropdown_button));
      test('should click on "Delete" action button', () => client.waitForExistAndClick(Files.action_button.replace('%B', 'Delete')));
      test('should accept the confirmation modal', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'file');
  },
  deleteFileWithBulkAction: function (filename) {
    scenario('Delete the created "File" with bulk action', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created files', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      });
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Select all" action', () => client.waitForExistAndClick(Files.bulk_actions_select_all_button));
      test('should click on "Bulk action" button', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Delete" action', () => client.waitForExistAndClick(Files.bulk_actions_delete_button));
      test('should accept the confirmation modal', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully deleted.'));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
    }, 'file');
  },
  viewFile: function (folderPath, filename, file) {
    scenario('View the created "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should search for the created files', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename));
      });
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(Files.dropdown_button));
      test('should click on "View" action button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Files.action_button.replace('%B', 'View')))
          .then(() => client.pause(2000));
      });
      test('should check that the file is well downloaded', () => client.checkFile(folderPath, file, 3000));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
    }, 'file');
  },
  checkFileFO: function (productName, filename, pause = 0) {
    scenario('Check that the file is well associated with the created product in the FO', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname, pause))
          .then(() => client.switchWindow(1));
      });
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should click on "Attachments" tab', () => client.scrollWaitForExistAndClick(productPage.attachments_tab, 50));
      test('should check the existence of the file', () => client.checkTextValue(productPage.filename_link, filename, 'equal', 5000));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
  },

  // if (sortBy === 'id') index = 2;
  // if (sortBy === 'name') index = 3;
  // if (sortBy === 'size') index = 5;
  // if (sortBy === 'associated') index = 6;
  sortFile: function (sortBy, index, filtredTable = false) {
    scenario('Sort files by "' + sortBy.toUpperCase() + '" in the Back Office', client => {
      test('should go to "Files" page', () => {
        return promise
          .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu))
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button, filtredTable))
          .then(() => client.getTextInVar(Files.files_number, 'filesNumber'));
      });
      test('should click on "Sort by ASC" icon', () => client.waitForExistAndClick(Files.sort_by_icon.replace("%H", index).replace("%BY", "up")));
      test('should check "Sort file by ' + sortBy + '"', () => {
        for (let j = 0; j < global.tab['filesNumber']; j++) {
          promise = client.getFileInformations(Files.files_table.replace('%R', j + 1).replace('%D', index), j);
        }
        return promise
          .then(() => client.checkSortFile(sortBy));
      });
      test('should click on "Sort by DESC" icon', () => client.waitForExistAndClick(Files.sort_by_icon.replace("%H", index).replace("%BY", "down")));
      test('should check "Sort file by ' + sortBy + '"', () => {
        for (let j = 0; j < global.tab['filesNumber']; j++) {
          promise = client.getFileInformations(Files.files_table.replace('%R', j + 1).replace('%D', index), j);
        }
        return promise
          .then(() => client.checkSortFile(sortBy));
      });
    }, 'file');
  },
  filterFile: function (searchValue, filterBy, index, sortedTable = false) {
    scenario('Filter files by "' + filterBy.toUpperCase() + '" in the Back Office', client => {
      test('should go to "Files" page', () => {
        return promise
          .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu))
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button, sortedTable));
      });
      test('should search "Files" by "' + filterBy + '"', () => {
        return promise
          .then(() => client.isVisible(Files.filter_name_input))
          .then(() => {
            if (filterBy === 'name') {
              client.searchByValue(Files.filter_name_input, Files.filter_search_button, searchValue);
            } else if (filterBy === 'size') {
              client.searchByValue(Files.filter_size_input, Files.filter_search_button, searchValue);
            } else if (filterBy === 'associated') {
              client.searchByValue(Files.filter_associated_input, Files.filter_search_button, searchValue);
            }
          })
          .then(() => client.isVisible(Files.empty_list, 1000))
          .then(() => client.getFilesNumber('table-attachment', 1000));
      });
      test('should check "Filter file by ' + filterBy + '"', () => {
        if (global.filesNumber > 0) {
          for (let j = 0; j < global.filesNumber; j++) {
            promise = client.getFileInformations(Files.files_table.replace('%R', j + 1).replace('%D', index), j, false);
          }
          // BOOM: 9607
          return promise
            .then(() => client.checkFilterFile(searchValue));
        } else {
          return Promise.reject(new Error('No Records Found')).then(expect(global.filesNumber).to.be.at.most(0));
        }
      });
    }, 'file');
  }
};