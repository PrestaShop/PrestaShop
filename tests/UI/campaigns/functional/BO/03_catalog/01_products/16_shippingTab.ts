import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabShippingPage,
  boProductsCreateTabStocksPage,
  boProductSettingsPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_shippingTab';

describe('BO - Catalog - Products : Shipping tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 10,
    status: true,
  });
  // Data to edit standard product
  const editProductData: FakerProduct = new FakerProduct({
    quantity: -10,
    packageDimensionWidth: 12,
    packageDimensionHeight: 12,
    packageDimensionDepth: 12,
    packageDimensionWeight: 12,
    deliveryTime: 'None',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
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

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.equals(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in shipping tab
  describe('Check all options in shipping tab', async () => {
    it('should go to shipping tab and edit package dimension', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension', baseContext);

      await boProductsCreateTabShippingPage.setPackageDimension(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should edit delivery time to \'None\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime', baseContext);

      await boProductsCreateTabShippingPage.setDeliveryTime(page, editProductData.deliveryTime);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that no delivery time is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTime', baseContext);

      const isDisplayed = await foClassicProductPage.isDeliveryTimeDisplayed(page);
      expect(isDisplayed).to.equal(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit delivery time to \'Default delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime2', baseContext);

      await boProductsCreateTabShippingPage.setDeliveryTime(page, 'Default delivery time');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that no delivery time is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTime1', baseContext);

      const isDisplayed = await foClassicProductPage.isDeliveryTimeDisplayed(page);
      expect(isDisplayed).to.equal(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on \'Edit delivery time\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditDeliveryTimeLink', baseContext);

      page = await boProductsCreateTabShippingPage.clickOnEditDeliveryTimeLink(page);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await boProductSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit delivery time to \'Specific delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryTime3', baseContext);

      await boProductsCreateTabShippingPage.setDeliveryTime(page, 'Specific delivery time');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should set delivery time of in-stock products and out-of-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTime', baseContext);

      await boProductsCreateTabShippingPage.setDeliveryTimeInStockProducts(page, '1 Day');
      await boProductsCreateTabShippingPage.setDeliveryTimeOutOfStockProducts(page, '12 Days');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time in stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock', baseContext);

      const deliveryTimeText = await foClassicProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('1 Day');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should update the product quantity to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityTo0', baseContext);

      await boProductsCreateTabStocksPage.setProductStock(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should allow order when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allowOrderWhenOutOfStock', baseContext);

      await boProductsCreateTabStocksPage.setOptionWhenOutOfStock(page, 'Allow orders');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time in stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock2', baseContext);

      const deliveryTimeText = await foClassicProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('12 Days');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should set additional shipping costs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAdditionalShippingCosts', baseContext);

      await boProductsCreateTabShippingPage.setAdditionalShippingCosts(page, 10);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to delivery address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

      const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
    });

    it('should select the first carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foClassicCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const shippingCost = await foClassicCheckoutPage.getShippingCost(page);
      expect(shippingCost).to.equal('€20.40');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should select available carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCarrier', baseContext);

      await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 1);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCartPage', baseContext);

      await foClassicProductPage.goToCartPage(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and check the shipping methods', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingMethods', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.eq([dataCarriers.clickAndCollect.name]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO6', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
