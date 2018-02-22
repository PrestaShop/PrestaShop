const {Menu} = require('../../../selectors/BO/menu.js');
const {DiscountSubMenu} = require('../../../selectors/BO/catalogpage/discount_submenu');
let promise = Promise.resolve();

module.exports = {
  createCatalogPriceRules(name, type, reduction, quantity = 1) {
    scenario('Create catalog price rules', client => {
      test('should go to "Discounts" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu , Menu.Sell.Catalog.discounts_submenu));
      test('should go to "Catalog Price Rules" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_price_rules_tab));
      test('should click on "Add new catalog price rule"', () => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.new_catalog_price_rules_button));
      test('should set the "Name" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.name_input, name));
      test('should set the "From quantity" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.form_quantity, quantity));
      test('should set the "Reduction type" input', () => client.waitAndSelectByValue(DiscountSubMenu.catalogPriceRules.reduction_type_select, type));
      test('should set the "Reduction" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.reduction_input, reduction));
      test('should click on "save" button', () => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.save_button));
    }, 'common_client');
  },
  deleteCatalogPriceRules(name){
    scenario('Delete catalog price rules "'+ name +'', client => {
      test('should go to "Discounts" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu , Menu.Sell.Catalog.discounts_submenu));
      test('should go to "Catalog Price Rules" page', () => {
        return promise
          .then(() => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_price_rules_tab))
          .then(() => client.isVisible(DiscountSubMenu.catalogPriceRules.search_name_input))
          .then(() => client.searchByName(name))
      });
      test('should delete the catalog rule price', () => {
        return promise
          .then(() => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.dropdown_button))
          .then(() => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.delete_button))
      });
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should check the success message appear', () => client.checkTextValue(DiscountSubMenu.catalogPriceRules.success_delete_message, 'Successful deletion.', "contain"));
    }, 'discount');
  }
};