// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import foHomePage from '@pages/FO/home';
import foProductPage from '@pages/FO/product';
import foLoginPage from '@pages/FO/login';
import cartPage from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import searchResultsPage from '@pages/FO/searchResults';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {PaymentMethods} from '@data/demo/paymentMethods';
import ProductFaker from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext = 'functional_BO_shopParameters_productSettings_productsStock_defaultPackStockManagement';

describe('BO - Shop Parameters - Product Settings : Default pack stock management', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstProductData: ProductFaker = new ProductFaker({type: 'Standard product', quantity: 40, reference: 'demo_test1'});
  const secondProductData: ProductFaker = new ProductFaker({type: 'Standard product', quantity: 30, reference: 'demo_test2'});
  const productPackData: ProductFaker = new ProductFaker({
    type: 'Pack of products',
    quantity: 15,
    pack: {demo_test1: 10, demo_test2: 5},
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Create 3 products', async () => {
    const tests = [
      {args: {productToCreate: firstProductData}},
      {args: {productToCreate: secondProductData}},
      {args: {productToCreate: productPackData}},
    ];
    tests.forEach((test, index) => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await productsPage.goToAddProductPage(page);

        const validationMessage = await addProductPage.createEditBasicProduct(page, test.args.productToCreate);
        await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  describe('Default pack stock', () => {
    const tests = [
      {
        args: {
          option: 'Decrement pack only.',
          packQuantity: productPackData.quantity - 1,
          firstProductQuantity: firstProductData.quantity,
          secondProductQuantity: secondProductData.quantity,
        },
      },
      {
        args: {
          option: 'Decrement products in pack only.',
          packQuantity: productPackData.quantity - 1,
          firstProductQuantity: firstProductData.quantity - productPackData.pack.demo_test1,
          secondProductQuantity: secondProductData.quantity - productPackData.pack.demo_test2,
        },
      },
      {
        args: {
          option: 'Decrement both.',
          packQuantity: productPackData.quantity - 2,
          firstProductQuantity: firstProductData.quantity - 2 * productPackData.pack.demo_test1,
          secondProductQuantity: secondProductData.quantity - 2 * productPackData.pack.demo_test2,
        },
      },
    ];
    tests.forEach((test, index) => {
      describe(`Test the option '${test.args.option}'`, async () => {
        it('should go to \'Shop parameters > Product Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

          await addProductPage.goToSubMenu(
            page,
            addProductPage.shopParametersParentLink,
            addProductPage.productSettingsLink,
          );

          const pageTitle = await productSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });

        it(`should choose the Default pack stock management '${test.args.option}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `stockManagementOption${index}`, baseContext);

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
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${index}`, baseContext);

          await foHomePage.goToLoginPage(page);

          const pageTitle = await foLoginPage.getPageTitle(page);
          await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foLoginPage.customerLogin(page, DefaultCustomer);

          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should create an order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createOrder${index}`, baseContext);

          // Go to home page
          await foLoginPage.goToHomePage(page);

          // search for the created pack and add go to product page
          await foHomePage.searchProduct(page, productPackData.name);
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
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

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

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageToCheck${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.catalogParentLink,
            dashboardPage.productsLink,
          );

          const pageTitle = await productsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productsPage.pageTitle);
        });

        it('should search for the pack of products and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterPackProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'name', productPackData.name);

          const packQuantity = await productsPage.getProductQuantityFromList(page, 1);
          await expect(packQuantity).to.equal(test.args.packQuantity);
        });

        it('should search for the first product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterFirstProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'name', firstProductData.name);

          const packQuantity = await productsPage.getProductQuantityFromList(page, 1);
          await expect(packQuantity).to.equal(test.args.firstProductQuantity);
        });

        it('should search for the second product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterSecondProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'name', secondProductData.name);

          const packQuantity = await productsPage.getProductQuantityFromList(page, 1);
          await expect(packQuantity).to.equal(test.args.secondProductQuantity);
        });
      });
    });
  });

  describe('Delete the 3 created products', async () => {
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

    const tests = [
      {args: {productToCreate: firstProductData}},
      {args: {productToCreate: secondProductData}},
      {args: {productToCreate: productPackData}},
    ];
    tests.forEach((test, index) => {
      it(`should delete product n°${index}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'name', test.args.productToCreate.name);

        const deleteTextResult = await productsPage.deleteProduct(page, test.args.productToCreate);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilters${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });
    });
  });
});
