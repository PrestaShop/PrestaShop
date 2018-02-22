const {Menu} = require('../../../selectors/BO/menu.js');
const {DiscountSubMenu} = require('../../../selectors/BO/catalogpage/discount_submenu');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
let promise = Promise.resolve();

module.exports = {
  createCatalogPriceRules(name, type, reduction, quantity = 1) {
    scenario('Create catalog price rules', client => {
      test('should go to "Discounts" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu));
      test('should go to "Catalog Price Rules" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_price_rules_tab));
      test('should click on "Add new catalog price rule"', () => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.new_catalog_price_rules_button));
      test('should set the "Name" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.name_input, name));
      test('should set the "From quantity" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.form_quantity, quantity));
      test('should set the "Reduction type" input', () => client.waitAndSelectByValue(DiscountSubMenu.catalogPriceRules.reduction_type_select, type));
      test('should set the "Reduction" input', () => client.waitAndSetValue(DiscountSubMenu.catalogPriceRules.reduction_input, reduction));
      test('should click on "save" button', () => client.waitForExistAndClick(DiscountSubMenu.catalogPriceRules.save_button));
    }, 'common_client');
  },
  deleteCatalogPriceRules(name) {
    scenario('Delete catalog price rules "' + name + '', client => {
      test('should go to "Discounts" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu));
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
  },

  /** Exemple of cart rule data
   * var cartRuleData = {
   *  name: 'cart_rule_name',
   *  customer_email: 'customer_email',
   *  minimum_amount: 'condition_minimum_amount',
   *  reduction_type: 'percent/amount',
   *  reduction: 'reduction_value'
   * };
   **/

  createCartRule(cartRuleData, promoCode) {
    scenario('Create a new "Cart Rule"', client => {
      test('should go to "Cart Rules" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu));
      test('should click on "Add new cart rule" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.new_cart_rule_button));
      test('should set the "Name" input', () => client.waitAndSetValue(DiscountSubMenu.cartRules.name_input, cartRuleData["name"]));
      test('should click on "Generate" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(DiscountSubMenu.cartRules.generate_button))
          .then(() => client.getAttributeInVar(DiscountSubMenu.cartRules.code_input, 'value', promoCode));
      });
      test('should click on "CONDITIONS" tab', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.conditions_tab));
      test('should choose the customer "John Doe"', () => client.chooseCustomer(DiscountSubMenu.cartRules.single_customer_input, DiscountSubMenu.cartRules.first_result_option, cartRuleData["customer_email"]));
      test('should set the "Minimum amount" input', () => client.waitAndSetValue(DiscountSubMenu.cartRules.minimum_amount_input, cartRuleData["minimum_amount"]));
      test('should click on "ACTIONS" tab', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.actions_tab));
      test('should switch the "Free shipping" to "Yes"', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.free_shipping));
      test('should click on "' + cartRuleData["reduction_type"] + '" radio', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.apply_discount_radio.replace("%T", cartRuleData["reduction_type"]), 2000));
      test('should set the "reduction" ' + cartRuleData["reduction_type"] + ' value', () => client.waitAndSetValue(DiscountSubMenu.cartRules.reduction_input.replace("%T", cartRuleData["reduction_type"]), cartRuleData["reduction"], 2000));
      test('should click on "Save" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.save_button, 2000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'discount');
  },
  deleteCartRule(name) {
    scenario('Delete "Cart Rule"', client => {
      test('should go to "Cart Rules" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu));
      test('should set the Catalog Price Rules name', () => client.waitAndSetValue(DiscountSubMenu.cartRules.search_name_input, name));
      test('should click on "Search" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.search_button));
      test('should click on the "dropdown toggle" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.dropdown_button));
      test('should click on the "Delete" button', () => client.waitForExistAndClick(DiscountSubMenu.cartRules.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(DiscountSubMenu.cartRules.success_delete_message, '×\nSuccessful deletion.'));
    }, 'discount');
  }
};