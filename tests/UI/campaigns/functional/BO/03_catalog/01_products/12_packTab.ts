import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabPackPage,
  boProductsCreateTabPricingPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicProductPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_packTab';

describe('BO - Catalog - Products : Pack Tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productStockDemo1: number = 0;
  let productStockDemo9: number = 0;

  const productNameEn: string = 'My pack';
  const productNameFr: string = 'Mon pack';
  const productRetailPrice: number = 25.00;
  // Automatically selected based on the mostly used tax on other products
  const mostUsedTaxValue: number = 20;
  const productQuantity: number = 4;
  const productStock: number = 100;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO - Create the product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it(`should filter the product "${dataProducts.demo_1.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo1', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_1.name);

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await boProductsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(dataProducts.demo_1.reference);

      productStockDemo1 = await boProductsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStockDemo1).to.be.gte(1);
    });

    it(`should filter the product "${dataProducts.demo_9.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo9', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_9.name);

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await boProductsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(dataProducts.demo_9.reference);

      productStockDemo9 = await boProductsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStockDemo9).to.be.gte(1);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBeforeNew', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePackProduct', baseContext);

      await boProductsPage.selectProductType(page, 'pack');

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductNameEn', baseContext);

      await boProductsCreatePage.setProductName(page, productNameEn, 'en');
      await boProductsCreatePage.setProductName(page, productNameFr, 'fr');
      await boProductsCreatePage.setProductStatus(page, true);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('BO - Edit properties of the pack', async () => {
    it('should go to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPackTab', baseContext);

      // The Pack Tab has the identifier `stock`
      await boProductsCreatePage.goToTab(page, 'stock');

      const isTabActive = await boProductsCreatePage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should add a product by product name to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByNameToPack', baseContext);

      await boProductsCreateTabPackPage.addProductToPack(page, 'shirt', 1);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const numberOfProducts = await boProductsCreateTabPackPage.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(1);

      const result = await boProductsCreateTabPackPage.getProductInPackInformation(page, 1);
      await Promise.all([
        expect(result.name).to.equal(
          `${dataProducts.demo_1.name}: `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[0].name)} - ${dataProducts.demo_1.attributes[0].values[0]}, `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[1].name)} - ${dataProducts.demo_1.attributes[1].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${dataProducts.demo_1.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should add a product by reference to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByRefToPack', baseContext);

      await boProductsCreateTabPackPage.addProductToPack(page, dataProducts.demo_14.reference, 1);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const numberOfProducts = await boProductsCreateTabPackPage.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await boProductsCreateTabPackPage.getProductInPackInformation(page, 2);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_14.name),
        expect(result.reference).to.equal(`Ref: ${dataProducts.demo_14.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should add set quantity for the first product in pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantityInPack', baseContext);

      await boProductsCreateTabPackPage.setProductQuantity(page, 0, productQuantity);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const numberOfProducts = await boProductsCreateTabPackPage.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await boProductsCreateTabPackPage.getProductInPackInformation(page, 1);
      await Promise.all([
        expect(result.name).to.equal(
          `${dataProducts.demo_1.name}: `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[0].name)} - ${dataProducts.demo_1.attributes[0].values[0]}, `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[1].name)} - ${dataProducts.demo_1.attributes[1].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${dataProducts.demo_1.reference}`),
        expect(result.quantity).to.equal(productQuantity),
      ]);
    });

    it('should delete the second product in pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductInPack', baseContext);

      await boProductsCreateTabPackPage.deleteProduct(page, 2, true);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const numberOfProducts = await boProductsCreateTabPackPage.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(1);
    });

    it('should add a product by reference to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductByRefToPack2', baseContext);

      await boProductsCreateTabPackPage.addProductToPack(page, dataProducts.demo_9.reference, 1);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const numberOfProducts = await boProductsCreateTabPackPage.getNumberOfProductsInPack(page);
      expect(numberOfProducts).to.equal(2);

      const result = await boProductsCreateTabPackPage.getProductInPackInformation(page, 2);
      await Promise.all([
        expect(result.name).to.equal(
          `${dataProducts.demo_9.name}: `
          + `${utilsCore.capitalize(dataProducts.demo_9.attributes[0].name)} - ${dataProducts.demo_9.attributes[0].values[0]}`,
        ),
        expect(result.reference).to.equal(`Ref: ${dataProducts.demo_9.reference}`),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should edit quantity to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantityPack', baseContext);

      await boProductsCreateTabPackPage.editQuantity(page, productStock);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to the Pricing tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPricingTab', baseContext);

      await boProductsCreatePage.goToTab(page, 'pricing');

      const isTabActive = await boProductsCreatePage.isTabActive(page, 'pricing');
      expect(isTabActive).to.be.eq(true);
    });

    it('should set the retail price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setRetailPrice', baseContext);

      await boProductsCreateTabPricingPage.setRetailPrice(page, true, productRetailPrice);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
      expect(productHeaderSummary.priceTaxExc).to.equals(`€${productRetailPrice.toFixed(2)} tax excl.`);

      const taxValue = utilsCore.percentage(productRetailPrice, mostUsedTaxValue);
      expect(productHeaderSummary.priceTaxIncl).to.equal(
        `€${(productRetailPrice + taxValue).toFixed(2)} tax incl. (tax rule: ${mostUsedTaxValue}%)`,
      );
    });

    it('should preview the product on front-office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPack', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productNameEn);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const productInformation = await foClassicProductPage.getProductInformation(page);
      const taxValue = utilsCore.percentage(productRetailPrice, mostUsedTaxValue);
      expect(productRetailPrice + taxValue).to.eq(productInformation.price);

      const productsPrice = await foClassicProductPage.getPackProductsPrice(page);
      const calculatedPrice = (
        ((
          (dataProducts.demo_1.price - (dataProducts.demo_1.price * (dataProducts.demo_1.specificPrice.discount / 100)))
          * (1 + (dataProducts.demo_1.tax / 100))
        ) * productQuantity)
        + dataProducts.demo_9.finalPrice
      ).toFixed(2);
      expect(calculatedPrice).to.eq(productsPrice.toString());

      const product1 = await foClassicProductPage.getProductInPackList(page, 1);
      await Promise.all([
        expect(product1.name).to.equals(
          `${dataProducts.demo_1.name} `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[0].name)}-${dataProducts.demo_1.attributes[0].values[0]} `
          + `${utilsCore.capitalize(dataProducts.demo_1.attributes[1].name)}-${dataProducts.demo_1.attributes[1].values[0]}`,
        ),
        expect(product1.price).to.equals(dataProducts.demo_1.finalPrice),
        expect(product1.quantity).to.equals(productQuantity),
      ]);

      const product2 = await foClassicProductPage.getProductInPackList(page, 2);
      await Promise.all([
        expect(product2.name).to.equals(
          `${dataProducts.demo_9.name} `
          + `${utilsCore.capitalize(dataProducts.demo_9.attributes[0].name)}-${dataProducts.demo_9.attributes[0].values[0]}`,
        ),
        expect(product2.price).to.equals(dataProducts.demo_9.finalPrice),
        expect(product2.quantity).to.equals(1),
      ]);
    });
  });

  describe('Order the pack on FO & check stock', async () => {
    it('should order the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPack1', baseContext);

      // Add product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);
      // Connect
      await foClassicCheckoutPage.clickOnSignIn(page);
      await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.be.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.be.eq(true);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBackOffice', baseContext);

      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);
      await page.reload();

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should return to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToPackTab', baseContext);

      // The Pack Tab has the identifier `stock`
      await boProductsCreatePage.goToTab(page, 'stock');

      const isTabActive = await boProductsCreatePage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should check the stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStock', baseContext);

      const stockValue = await boProductsCreateTabPackPage.getStockValue(page);
      expect(stockValue).to.be.equals(productStock - 1);
    });
  });

  describe(`Set the order to ${dataOrderStatuses.delivered.name} & check stock`, async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle: string = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult: string = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
      expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await boProductsPage.goToProductPage(page);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should return to the Pack tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToPackTab1', baseContext);

      // The Pack Tab has the identifier `stock`
      await boProductsCreatePage.goToTab(page, 'stock');

      const isTabActive = await boProductsCreatePage.isTabActive(page, 'stock');
      expect(isTabActive).to.be.eq(true);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRecentStockMovement', baseContext);

      const result = await boProductsCreateTabPackPage.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains('Shipped products'),
        expect(result.employee).to.equal(''),
        expect(result.quantity).to.equal(-1),
      ]);
    });
  });

  describe('Decrement products in pack only - Configure, Order & Check stocks', async () => {
    it('should edit Pack Quantities "Decrement products in pack only"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackQuantities', baseContext);

      await boProductsCreateTabPackPage.editPackStockType(page, 'Use quantity of products in the pack');

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview the product on front-office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPack2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productNameEn);
    });

    it('should order the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPack2', baseContext);

      // Add product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete).to.be.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete).to.be.eq(true);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBackOffice1', baseContext);

      page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnProductsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it(`should filter and check the product "${dataProducts.demo_1.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo11', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_1.name);

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await boProductsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(dataProducts.demo_1.reference);

      const productStock = await boProductsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStock).to.be.gte(1);

      expect(productStock).to.be.eq(productStockDemo1 - productQuantity);
    });

    it(`should filter and check the product "${dataProducts.demo_9.reference}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductDemo91', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_9.name);

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.gte(1);

      const productReference = await boProductsPage.getTextColumn(page, 'reference', 1);
      expect(productReference).to.be.eq(dataProducts.demo_9.reference);

      const productStock = await boProductsPage.getTextColumn(page, 'quantity', 1) as number;
      expect(productStock).to.be.gte(1);

      expect(productStock).to.be.eq(productStockDemo9 - 1);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  deleteProductTest(new FakerProduct({
    name: productNameEn,
  }), `${baseContext}_postTest_0`);
});
