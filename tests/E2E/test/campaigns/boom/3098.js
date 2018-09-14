const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {Menu} = require('../../selectors/BO/menu.js');
let promise = Promise.resolve();

let combinationProductData = {
  name: 't-shirt',
  quantity: "50",
  price: '5',
  image_green_tshirt: 'greentshirt.jpg',
  image_red_tshirt: 'redtshirt.jpg',
  reference: 'a'
};

let packProductData = {
  name: 'packData',
  quantity: "5",
  price: '5',
  pack: {
    product: "t-shirt",
  },
};

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-3098
 **/

scenario('Create a product with two combinations', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  scenario('Create a product "t-shirt" with two combinations', client => {
    scenario('Add product Basic settings', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "New Product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
      test('should select the "Product with combination" option', () => client.waitForExistAndClick(AddProductPage.product_combinations));
      test('should set the "product name"', () => client.waitAndSetValue(AddProductPage.product_name_input, combinationProductData.name + date_time));
      test('should set the "Price" input', () => client.setPrice(AddProductPage.priceTE_shortcut, combinationProductData.price));
      test('should upload the "green t-shirt" picture', () => client.uploadPicture(combinationProductData.image_green_tshirt, AddProductPage.picture));
      test('should upload the "red t-shirt" product picture', () => client.uploadPicture(combinationProductData.image_red_tshirt, AddProductPage.picture));
    }, 'product/product');

    scenario('Add product combinations', client => {
      test('should click on "Combinations"', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
      test('should click on "Green" Color', () => client.waitForExistAndClick(AddProductPage.combination_color_green));
      test('should click on "Red" Color', () => client.waitForExistAndClick(AddProductPage.combination_color_red));
      test('should click on "Generate" button', () => client.waitForExistAndClick(AddProductPage.combination_generate_button));

      test('should check that the success alert message is well displayed', () => {
        return promise
          .then(() => client.waitForVisibleAndClick(AddProductPage.close_validation_button))
          .then(() => client.pause(3000));
      });
      test('should click on "Edit" first combination', () => {
        return promise
          .then(() => client.getCombinationData(1))
          .then(() => client.goToEditCombination());
      });
      test('should set the "Red color combination" quantity', () => client.waitAndSetValue(AddProductPage.combination_quantity.replace("%NUMBER", combinationId), combinationProductData.quantity));
      test('should click on "Red T-shirt" image', () => {
        return promise
          .then(() => client.getAttributeInVar((AddProductPage.image_combination_src.replace("%ID", combinationId)).replace("%POS", "2"), "src", "red"))
          .then(() => client.scrollWaitForExistAndClick((AddProductPage.image_combination_src.replace("%ID", combinationId)).replace("%POS", "2"), 50));
      });
      test('should go back to combination list', () => client.backToProduct());
      test('should click on "Edit" second combination', () => {
        return promise
          .then(() => client.getCombinationData(2))
          .then(() => client.goToEditCombination());
      });
      test('should set the "Green color combination" quantity', () => {
        return promise
          .then(() => client.waitAndSetValue(AddProductPage.combination_quantity.replace("%NUMBER", combinationId), combinationProductData.quantity))
          .then(() => client.getAttributeInVar((AddProductPage.image_combination_src.replace("%ID", combinationId)).replace("%POS", "1"), "src", "green"));
      });
    }, 'product/create_combinations');

    scenario('Save Product', client => {
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.symfony_toolbar))
          .then(() => {
            if (global.isVisible) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          });
      });
      test('should set the product "online"', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
      test('should click on "SAVE"', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    }, 'product/product');
  }, 'product/product');

  scenario('Create a pack of product "t-shirt"', () => {
    scenario('Add product Basic settings', client => {
      test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "New Product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
      test('should set the "product name"', () => client.waitAndSetValue(AddProductPage.product_name_input, packProductData.name + date_time));
      test('should select the "Pack of products"', () => client.waitAndSelectByValue(AddProductPage.product_type, 1));
      test('should set the "green T-shirt" product to the pack', () => client.addPackProduct(packProductData.pack.product + date_time, 5));
      test('should check the Link of the "green T-shirt" image product', () => {
        return promise
          .then(() => client.UrlModification("green", combinationProductData.name + date_time))
          .then(() => client.checkAttributeValue(AddProductPage.img_added_product_pack.replace("%ID", '1'), "src", global.tab["green"].replace("small_default", "home_default"), "contain"));
      });
      test('should set the "red T-shirt" product to the pack', () => client.addPackProduct(packProductData.pack.product + date_time, 5));
      test('should check the Link of the "red T-shirt" image product', () => {
        return promise
          .then(() => client.UrlModification("red", combinationProductData.name + date_time))
          .then(() => client.checkAttributeValue(AddProductPage.img_added_product_pack.replace("%ID", '2'), "src", global.tab["red"].replace("small_default", "home_default"), "contain"));
      });
    }, 'product/product');
  }, 'product/product');

}, 'product/product', true);
