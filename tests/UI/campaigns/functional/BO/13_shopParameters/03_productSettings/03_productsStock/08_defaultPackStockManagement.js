require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const foProductPage = require('@pages/FO/product');
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const ProductFaker = require('@data/faker/product');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_productsStock_defaultPackStockManagement';


let browserContext;
let page;
const productData = new ProductFaker({type: 'Pack of products', quantity: 15, pack: {demo_1: 10, demo_2: 20}});

describe('Default pack stock management', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  /*after(async () => {
    await helper.closeBrowserContext(browserContext);
  });*/

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Create a product', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to create product page and create a product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);
      const validationMessage = await addProductPage.createEditBasicProduct(page, productData);

      await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  describe('Default pack stock', () => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.shopParametersParentLink,
        addProductPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    const tests = [
      {args: {option: 'Decrement pack only.'}},
      /*{args: {option: 'Decrement products in pack only.'}},
      {args: {option: 'Decrement both.'}},*/
    ];
    tests.forEach((test, index) => {
      describe(`Check the option '${test.args.option}'`, async () => {
        it(`should choose the Default pack stock management '${test.args.option}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

          const result = await productSettingsPage.setDefaultPackStockManagement(page, test.args.option);
          await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await productSettingsPage.viewMyShop(page);

          await foHomePage.changeLanguage(page, 'en');

          const isFoHomePage = await foHomePage.isHomePage(page);
          await expect(isFoHomePage, 'Fail to open FO home page').to.be.true;
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${test.args.action}`, baseContext);

          await foHomePage.goToLoginPage(page);
          const pageTitle = await foLoginPage.getPageTitle(page);
          await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${test.args.action}`, baseContext);

          await foLoginPage.customerLogin(page, DefaultAccount);
          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should create an order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createOrder${test.args.action}`, baseContext);

          // Go to home page
          await foLoginPage.goToHomePage(page);

          // search for the created pack and add go to product page
          await foHomePage.searchProduct(page, productData.name);
          await searchResultsPage.goToProductPage(page, 1);

          // Add the created product to the cart
          await foProductPage.addProductToTheCart(page);

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);

          // Address step - Go to delivery step
          const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
          await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

          // Payment step - Choose payment step
          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${test.args.action}`, baseContext);

          await orderConfirmationPage.logout(page);
          const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is connected').to.be.false;
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await productSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });

        it('should go to the created product page and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterProductByName', baseContext);

          await productsPage.filterProducts(page, 'name', productData.name);
          await productsPage.goToEditProductPage(page, 1);
        });
      });
    });
  });

  /*describe('Delete the product created for test', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteTextResult = await productsPage.deleteProduct(page, productData);
      await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await productsPage.resetFilterCategory(page);
      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });*/
});
