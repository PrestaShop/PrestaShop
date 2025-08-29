import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductSettingsPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_defaultPackStockManagement';

describe('BO - Shop Parameters - Product Settings : Default pack stock management', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstProductData: FakerProduct = new FakerProduct({type: 'standard', quantity: 40, reference: 'demo_test1'});
  const secondProductData: FakerProduct = new FakerProduct({type: 'standard', quantity: 30, reference: 'demo_test2'});
  const productPackData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);
      });

      it('should select product type and click on add new product button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `selectProductType${index}`, baseContext);

        await boProductsPage.selectProductType(page, test.args.productToCreate.type);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should go to create product page and create a product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        const validationMessage = await boProductsCreatePage.setProduct(page, test.args.productToCreate);
        expect(validationMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });
    });
  });

  describe('Default pack stock', () => {
    const tests = [
      {
        args: {
          option: 'Use pack quantity',
          packQuantity: productPackData.quantity - 1,
          firstProductQuantity: firstProductData.quantity,
          secondProductQuantity: secondProductData.quantity,
        },
      },
      {
        args: {
          option: 'Use quantity of products in the pack',
          packQuantity: productPackData.quantity - 1,
          firstProductQuantity: firstProductData.quantity - productPackData.pack[0].quantity,
          secondProductQuantity: secondProductData.quantity - productPackData.pack[1].quantity,
        },
      },
      {
        args: {
          option: 'Use both, whatever is lower',
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

          await boProductsCreatePage.goToSubMenu(
            page,
            boProductsCreatePage.shopParametersParentLink,
            boProductsCreatePage.productSettingsLink,
          );

          const pageTitle = await boProductSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
        });

        it(`should choose the Default pack stock management '${test.args.option}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `stockManagementOption${index}`, baseContext);

          const result = await boProductSettingsPage.setDefaultPackStockManagement(page, test.args.option);
          expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await boProductSettingsPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isFoHomePage = await foClassicHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${index}`, baseContext);

          await foClassicHomePage.goToLoginPage(page);

          const pageTitle = await foClassicLoginPage.getPageTitle(page);
          expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

          const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
        });

        it('should go to home page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToHomePage${index}`, baseContext);

          // Go to home page
          await foClassicLoginPage.goToHomePage(page);

          const isFoHomePage = await foClassicHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should search for the created product and go to product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCreatedProductPage${index}`, baseContext);

          // search for the created pack and add go to product page
          await foClassicHomePage.searchProduct(page, productPackData.name);
          await foClassicSearchResultsPage.goToProductPage(page, 1);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle.toUpperCase()).to.contains(productPackData.name.toUpperCase());
        });

        it('should add product to cart and proceed to checkout', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Add the created product to the cart
          await foClassicProductPage.addProductToTheCart(page);

          // Proceed to checkout the shopping cart
          await foClassicCartPage.clickOnProceedToCheckout(page);
        });

        it('should go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          // Address step - Go to delivery step
          const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
          expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should go to payment step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
          expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
        });

        it('should confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmTheOrder${index}`, baseContext);

          // Payment step - Choose payment step
          await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
          expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

          await foClassicCheckoutOrderConfirmationPage.logout(page);

          const isCustomerConnected = await foClassicCheckoutOrderConfirmationPage.isCustomerConnected(page);
          expect(isCustomerConnected, 'Customer is connected').to.eq(false);
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boProductSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageToCheck${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.catalogParentLink,
            boDashboardPage.productsLink,
          );

          const pageTitle = await boProductsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsPage.pageTitle);
        });

        it('should search for the pack of products and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterPackProductByName${index}`, baseContext);

          await boProductsPage.resetFilter(page);
          await boProductsPage.filterProducts(page, 'product_name', productPackData.name);

          const packQuantity = await boProductsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.packQuantity);
        });

        it('should search for the first product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterFirstProductByName${index}`, baseContext);

          await boProductsPage.resetFilter(page);
          await boProductsPage.filterProducts(page, 'product_name', firstProductData.name);

          const packQuantity = await boProductsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.firstProductQuantity);
        });

        it('should search for the second product in the pack and check the quantity', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterSecondProductByName${index}`, baseContext);

          await boProductsPage.resetFilter(page);
          await boProductsPage.filterProducts(page, 'product_name', secondProductData.name);

          const packQuantity = await boProductsPage.getTextColumn(page, 'quantity', 1);
          expect(packQuantity).to.equal(test.args.secondProductQuantity);
        });
      });
    });
  });

  describe('Delete the 3 created products', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await boProductSettingsPage.goToSubMenu(
        page,
        boProductSettingsPage.catalogParentLink,
        boProductSettingsPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    const tests = [
      {args: {productToCreate: firstProductData}},
      {args: {productToCreate: secondProductData}},
      {args: {productToCreate: productPackData}},
    ];
    tests.forEach((test, index: number) => {
      it(`should delete product n°${index}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', test.args.productToCreate.name);

        const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
        expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilters${index}`, baseContext);

        const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });
    });
  });
});
