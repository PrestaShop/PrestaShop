// Importing pages
const FO_PAGE = require('../../pages/FO/FO_home');
const FO_PRODUCT_Page = require('../../pages/FO/FO_product');
const FO_CART_Page = require('../../pages/FO/FO_cart');

let page;
let FO_HOME;
let FO_PRODUCT;
let FO_CART;

let productData = {
  name: 'T-SHIRT IMPRIMÉ COLIBRI',
  quantity: '1',
  price: '22.94',
  description: 'Symbole de légèreté et de délicatesse',
};

let cartData = {
  name: 'T-shirt imprimé colibri',
  quantity: "1",
  price: '22,94',
};

// Init objects needed
const init = async () => {
  page = await global.browser.newPage();
  FO_HOME = await (new FO_PAGE(page));
  FO_PRODUCT = await (new FO_PRODUCT_Page(page));
  FO_CART = await (new FO_CART_Page(page));
};

// Scenario
global.scenario('Check the Front Office', () => {
  test('should open the shop page', async () => {
    await FO_HOME.goTo(global.URL_FO);
    await FO_HOME.checkHomePage()
  });
  test('should go to the first product page', () => FO_HOME.goToProductPage('1'));
  test('should check the product page', () => FO_PRODUCT.checkProduct(productData));
  test('should add product to the cart', () => FO_PRODUCT.addProductToTheCart());
  test('should check the cart details', () => FO_CART.checkCartDetails(cartData, '1'));
  test('should checkout a product', () => FO_CART.clickOnProceedToCheckout());
}, init, true);
