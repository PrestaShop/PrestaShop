var CommonClient = require('./common_client');
const {DiscountSubMenu} = require('../selectors/BO/catalogpage/discount_submenu');

class Discount extends CommonClient {

  searchByName(catalogPriceRulesName) {
    if (isVisible) {
      return this.client
        .waitAndSetValue(DiscountSubMenu.catalogPriceRules.search_name_input, catalogPriceRulesName)
        .waitForExistAndClick(DiscountSubMenu.catalogPriceRules.search_button)
    } else {
      return this.client.pause(1000)
    }
  }
}

module.exports = Discount;