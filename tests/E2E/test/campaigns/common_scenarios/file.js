const {Files} = require('../../selectors/BO/catalogpage/files');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {Menu} = require('../../selectors/BO/menu.js');
const {productPage} = require('../../selectors/FO/product_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const common_scenarios = require('../common_scenarios/product');
let promise = Promise.resolve();

module.exports = {
  createFile: function (filename, description, file) {
    scenario('Create a new "File"', client => {
      test('should go to "Files" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu));
      test('should click on "Add new file" button', () => client.waitForExistAndClick(Files.add_new_file_button));
      test('should set the "Filename" input', () => client.waitAndSetValue(Files.filename_input, filename + global.date_time));
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
      test('should set the "Filename" input', () => client.waitAndSetValue(Files.filename_input, updatedFilename + global.date_time));
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
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename + global.date_time));
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Files.edit_button));
      test('should check that the "Filename" value is equal to "' + filename + '"', () => client.checkAttributeValue(Files.filename_input, 'value', filename + global.date_time));
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
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename + global.date_time));
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
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, global.date_time));
      });
      test('should click on "Bulk actions" button', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Select all" checkbox', () => client.waitForExistAndClick(Files.bulk_actions_select_all_button));
      test('should click on "Bulk actions" checkbox', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Unselect all" checkbox', () => client.waitForExistAndClick(Files.bulk_actions_unselect_all_button));
      test('should click on "Bulk actions" button', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Select all" action', () => client.waitForExistAndClick(Files.bulk_actions_select_all_button));
      test('should click on "Bulk actions" button', () => client.waitForExistAndClick(Files.bulk_action_button));
      test('should click on "Delete" checkbox', () => client.waitForExistAndClick(Files.bulk_actions_delete_button));
      test('should accept the confirmation modal', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully deleted.'));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button, 3000))
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
          .then(() => client.searchByValue(Files.filter_name_input, Files.filter_search_button, filename + global.date_time));
      });
      test('should click on "Dropdown toggle" button', () => client.waitForExistAndClick(Files.dropdown_button));
      test('should click on "View" action button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Files.action_button.replace('%B', 'View')))
          .then(() => client.pause(2000));
      });
      test('should check that the file is well downloaded', () => client.checkFile(folderPath, file, 4000));
      test('should click on "Reset" button', () => {
        return promise
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button));
      });
    }, 'file');
  },
  checkFileFO: function (productData, fileEditedData, existingFile, editingFile, id) {
    scenario('Check that the file is well associated with the created product in the FO', client => {
      if (existingFile) {
        test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons, 1000));
        test('should go to the Front Office', () => client.switchWindow(id));
        common_scenarios.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
        test('should click on "Attachments" tab', () => client.scrollWaitForExistAndClick(productPage.attachments_tab, 50));
        test('should check the existence of the file', () => {
          for (let i = 0; i < productData.options.filename.length; i++) {
            promise = client.checkTextValue(productPage.filename_link.replace('%N', i + 1), productData.options.filename[i] + global.date_time, 'equal', 2000);
          }
          return promise
            .then(() => client.pause(1000));
        });
      } else if (editingFile) {
        test('should go to the Front Office', () => {
          return promise
            .then(() => client.switchWindow(1))
            .then(() => client.refresh());
        });
      }
      else {
        test('should go to the Front Office', () => {
          return promise
            .then(() => client.switchWindow(id))
            .then(() => client.refresh());
        });
        test('should check the nonexistence of the file(s)', () => client.isNotExisting(productPage.attachments_tab, 1000));
      }
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
  },

  // if (sortBy === 'id') index = 2;
  // if (sortBy === 'name') index = 3;
  // if (sortBy === 'size') index = 5;
  // if (sortBy === 'associated') index = 6;
  sortFile: function (selector, sortBy, index, isNumber = false, filtredTable = false) {
    scenario('Sort files by "' + sortBy.toUpperCase() + '" in the Back Office', client => {
      test('should go to "Files" page', () => {
        return promise
          .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu))
          .then(() => client.isVisible(Files.filter_reset_button))
          .then(() => client.resetButton(Files.filter_reset_button, filtredTable))
          .then(() => client.getTextInVar(Files.files_number, 'filesNumber'));
      });
      test('should click on "Sort by ASC" icon', async () => {
        global.elementsSortedTable = [];
        global.elementsTable = [];
        for (let j = 0; j < (parseInt(tab['filesNumber'])); j++) {
          await client.getTableField(selector, j);
        }
        await client.waitForExistAndClick(Files.sort_by_icon.replace("%H", index).replace("%BY", "up"));
      });
      test('should check that the files are well sorted by ASC', async () => {
        for (let j = 0; j < (parseInt(tab['filesNumber'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber);
      });
      test('should click on "Sort by DESC" icon', () => client.waitForExistAndClick(Files.sort_by_icon.replace("%H", index).replace("%BY", "down")));
      test('should check that the files are well sorted by DESC', async () => {
        for (let j = 0; j < (parseInt(tab['filesNumber'])); j++) {
          await client.getTableField(selector, j, true);
        }
        await client.checkSortTable(isNumber, 'DESC');
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
            // Issue:11054
            else if (filterBy === 'size') {
              client.searchByValue(Files.filter_size_input, Files.filter_search_button, searchValue);
            }
          })
          .then(() => client.isVisible(Files.empty_list, 1000))
          .then(() => client.getFilesNumber('table-attachment', 1000));
      });
    }, 'file');
  }
};
