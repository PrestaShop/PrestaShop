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
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import ProductData from '@data/faker/product';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_defaultPackStockManagement';

describe('BO - Shop Parameters - Product Settings : Default pack stock management', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstProductData: ProductData = new ProductData({type: 'standard', quantity: 40, reference: 'demo_test1'});
  const secondProductData: ProductData = new ProductData({type: 'standard', quantity: 30, reference: 'demo_test2'});
  const productPackData: ProductData = new ProductData({
    type: 'pack',
    quantity: 15,
    pack: [
      {
        reference: 'demo_test1',
        quantity: 10,
      },
      {
        reference: 'demo_test2',
        quantity: 5,
      },
    ],
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
    tests.forEach((test, index: number) => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

        const isModalVisible = await productsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);
      });

      it('should select product type and click on add new product button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `selectProductType${index}`, baseContext);

        await productsPage.selectProductType(page, test.args.productToCreate.type);

        await productsPage.clickOnAddNewProduct(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should go to create product page and create a product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        const validationMessage = await addProductPage.setProduct(page, test.args.productToCreate);
        expect(validationMessage).to.equal(addProductPage.successfulUpdateMessage);
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
          firstProductQuantity: firstProductData.quantity - productPackData.pack[0].quantity,
          secondProductQuantity: secondProductData.quantity - productPackData.pack[1].quantity,
        },
      },
      {
        args: {
          option: 'Decrement both.',
          packQuantity: productPackData.quantity - 2,
          firstProductQuantity: firstProductData.quantity - 2 * productPackData.pack[0].quantity,
          secondProductQuantity: secondProductData.quantity - 2 * productPackData.pack[1].quantity,
        },
      },
    ];
    tests.forEach((test, index: number) => {
      describe(`Test the option '${test.args.option}'`, async () => {
        it('should go to \'Shop parameters > Product Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

          await addProductPage.goToSubMenu(
            page,
            addProductPage.shopParametersParentLink,
            addProductPage.productSettingsLink,
          );

          const pageTitle = await productSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });

        it(`should choose the Default pack stock management '${test.args.option}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `stockManagementOption${index}`, baseContext);

          const result = await productSettingsPage.setDefaultPackStockManagement(page, test.args.option);
          expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await productSettingsPage.viewMyShop(page);
          await foHomePage.changeLanguage(page, 'en');

          const isFoHomePage = await foHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${index}`, baseContext);

          await foHomePage.goToLoginPage(page);

          const pageTitle = await foLoginPage.getPageTitle(page);
          expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should go to home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToHomePage${index}`, baseContext);

          // Go to home page
          await foLoginPage.goToHomePage(page);

          const isFoHomePage = await foHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should search for the created product and go to product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCreatedProductPage${index}`, baseContext);

          // search for the created pack and add go to product page
          await foHomePage.searchProduct(page, productPackData.name);
          await searchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foProductPage.getPageTitle(page);
          expect(pageTitle.toUpperCase()).to.contains(productPackData.name.toUpperCase());
        });

        it('should add product to cart and proceed to checkout', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Add the created product to the cart
          await foProductPage.addProductToTheCart(page);

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);
        });

        it('should go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          // Address step - Go to delivery step
          const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
          expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should go to payment step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmTheOrder${index}`, baseContext);

          // Payment step - Choose payment step
          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

          await orderConfirmationPage.logout(page);

          const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is connected').to.eq(false);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foProductPage.closePage(browserContext, page, 0);

          const pageTitle = await productSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageToCheck${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.catalogParentLink,
            dashboardPage.productsLink,
          );

          const pageTitle = await productsPage.getPageTitle(page);
          expect(pageTitle).to.contains(productsPage.pageTitle);
        });

        it('should search for the pack of products and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterPackProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'product_name', productPackData.name);

          const packQuantity = await productsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.packQuantity);
        });

        it('should search for the first product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterFirstProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'product_name', firstProductData.name);

          const packQuantity = await productsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.firstProductQuantity);
        });

        it('should search for the second product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterSecondProductByName${index}`, baseContext);

          await productsPage.resetFilter(page);
          await productsPage.filterProducts(page, 'product_name', secondProductData.name);

          const packQuantity = await productsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.secondProductQuantity);
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    const tests = [
      {args: {productToCreate: firstProductData}},
      {args: {productToCreate: secondProductData}},
      {args: {productToCreate: productPackData}},
    ];
    tests.forEach((test, index: number) => {
      it(`should delete product n°${index}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'product_name', test.args.productToCreate.name);

        const isModalVisible = await productsPage.clickOnDeleteProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        const textMessage = await productsPage.clickOnConfirmDialogButton(page);
        expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilters${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });
    });
  });
});
