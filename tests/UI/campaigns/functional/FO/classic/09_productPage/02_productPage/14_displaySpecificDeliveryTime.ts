import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabShippingPage,
  boProductsCreateTabStocksPage,
  type BrowserContext,
  FakerProduct,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displaySpecificDeliveryTime';

describe('FO - Product page - Product page : Display Specific delivery time', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create new product
  const newProductData: FakerProduct = new FakerProduct({
    name: 'test',
    type: 'standard',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create standard product', async () => {
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
      expect(isModalVisible).to.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePackOfProducts', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should choose the option \'Allow orders\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAllowOrders', baseContext);

      await boProductsCreatePage.goToTab(page, 'stock');
      await boProductsCreateTabStocksPage.setOptionWhenOutOfStock(page, 'Allow orders');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should choose the option \'Specific delivery time\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSpecificDeliveryTime', baseContext);

      await boProductsCreatePage.goToTab(page, 'shipping');
      await boProductsCreateTabShippingPage.setDeliveryTime(page, 'Specific delivery time');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should set delivery time out-of-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTimeOutOfStock', baseContext);

      await boProductsCreateTabShippingPage.setDeliveryTimeOutOfStockProducts(page, 'Hello');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Check Specific delivery time on Product page', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time out of stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeOutOfStock', baseContext);

      const deliveryTimeText = await foClassicProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('Hello');
    });
  });

  describe('Edit specific delivery time of the created product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the product quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity', baseContext);

      await boProductsCreatePage.goToTab(page, 'stock');
      await boProductsCreateTabStocksPage.setProductQuantity(page, 100);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should set delivery time of in-stock products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setDeliveryTime2', baseContext);

      await boProductsCreatePage.goToTab(page, 'shipping');
      await boProductsCreateTabShippingPage.setDeliveryTimeInStockProducts(page, 'Delivered in less than a week');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Check Specific delivery time on Product page', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the delivery time out of stock product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryTimeInStock2', baseContext);

      const deliveryTimeText = await foClassicProductPage.getDeliveryInformationText(page);
      expect(deliveryTimeText).to.equal('Delivered in less than a week');
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
