const {Menu} = require('../../selectors/BO/menu.js');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {Suppliers} = require('../../selectors/BO/catalogpage/Manufacturers/suppliers');

module.exports = {
  createSupplier: function (supplierData) {
    scenario('Create a new "Suppliers"', client => {
      test('should go to "Brands & Suppliers" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu));
      test('should click on "Suppliers" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.supplier_tab));
      test('should click on "Add new supplier" button', () => client.waitForExistAndClick(Suppliers.new_supplier_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Suppliers.name_input, supplierData.name + date_time));
      test('should set the "Address" input', () => client.waitAndSetValue(Suppliers.address_input, supplierData.address));
      test('should set the "City" input', () => client.waitAndSetValue(Suppliers.city_input, supplierData.city));
      test('should set the "Meta title" input', () => client.waitAndSetValue(Suppliers.meta_title_input, supplierData.metaTitle));
      test('should set the "Meta description" input', () => client.waitAndSetValue(Suppliers.meta_description_input, supplierData.metaDescription));
      test('should click on "Activate" button', () => client.waitForExistAndClick(Suppliers.active_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(Suppliers.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, 'Successful creation.','contain'));
    }, 'common_client');
  },
};
