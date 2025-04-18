import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabStocksPage,
  boStockMovementsPage,
  type BrowserContext,
  dataEmployees,
  FakerProduct,
  foClassicProductPage,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_stocksTab';

describe('BO - Catalog - Products : Stocks tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
    quantity: 0,
  });
  const productQuantity: number = 300;
  const productMinimalQuantity: number = 5;
  const productStockLocation: string = 'Second floor';
  const productLowStockAlertByEmail: boolean = true;
  const productLowStockThreshold: number = 3;
  const productLabelWhenInStock: string = 'LABEL IN STOCK';
  const productLabelWhenOutOfStock: string = 'LABEL OUT OF STOCK';
  const todayDate: string = utilsDate.getDateFormat('yyyy-mm-dd');

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
      expect(isModalVisible).to.eq(true);
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

  // 2 - Check all options in Stock tab
  describe('Check all options in Stock tab', async () => {
    it('should go to the Stocks tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksTab', baseContext);

      await boProductsCreatePage.goToTab(page, 'stock');

      const isTabActive = await boProductsCreatePage.isTabActive(page, 'stock');
      expect(isTabActive).to.eq(true);
    });

    it('should add quantity to stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addQuantityToStock', baseContext);

      await boProductsCreateTabStocksPage.setQuantityDelta(page, productQuantity);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
      expect(productHeaderSummary.quantity).to.equal(`${productQuantity} in stock`);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result = await boProductsCreateTabStocksPage.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${dataEmployees.defaultEmployee.firstName} ${dataEmployees.defaultEmployee.lastName}`),
        expect(result.quantity).to.equal(productQuantity),
      ]);
    });

    it('should click on View all stock movements', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickViewAllStockMovements', baseContext);

      page = await boProductsCreateTabStocksPage.clickViewAllStockMovements(page);

      const pageTitle = await boStockMovementsPage.getPageTitle(page);
      expect(pageTitle).to.equal(boStockMovementsPage.pageTitle);
    });

    it('should close the Stock Movements page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeStockMovementsPage', baseContext);

      page = await boStockMovementsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should fill Stocks values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillStockValues', baseContext);

      await boProductsCreateTabStocksPage.setMinimalQuantity(page, productMinimalQuantity);
      await boProductsCreateTabStocksPage.setStockLocation(page, productStockLocation);
      await boProductsCreateTabStocksPage.setLowStockAlertByEmail(page, productLowStockAlertByEmail, productLowStockThreshold);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check Stocks values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockValues', baseContext);

      const valueMinimalQuantity = await boProductsCreateTabStocksPage.getValue(page, 'minimal_quantity');
      expect(valueMinimalQuantity).to.eq(productMinimalQuantity.toString());

      const valueStockLocation = await boProductsCreateTabStocksPage.getValue(page, 'location');
      expect(valueStockLocation).to.eq(productStockLocation);

      const valueLowStockAlertByEmail = await boProductsCreateTabStocksPage.getValue(page, 'low_stock_threshold_enabled');
      expect(valueLowStockAlertByEmail).to.eq(productLowStockAlertByEmail ? '1' : '0');

      const valueLowStockThreshold = await boProductsCreateTabStocksPage.getValue(page, 'low_stock_threshold');
      expect(valueLowStockThreshold).to.eq(productLowStockThreshold.toString());
    });

    it('should fill When out of stock values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillWhenOutOfStockValues', baseContext);

      await boProductsCreateTabStocksPage.setLabelWhenInStock(page, productLabelWhenInStock);
      await boProductsCreateTabStocksPage.setLabelWhenOutOfStock(page, productLabelWhenOutOfStock);
      await boProductsCreateTabStocksPage.setAvailabilityDate(page, todayDate);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check When out of stock values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWhenOutOfStockValues', baseContext);

      const valueLabelWhenInStock = await boProductsCreateTabStocksPage.getValue(page, 'available_now', '1');
      expect(valueLabelWhenInStock).to.eq(productLabelWhenInStock);

      const valueLabelWhenOutOfStock = await boProductsCreateTabStocksPage.getValue(page, 'available_later', '1');
      expect(valueLabelWhenOutOfStock).to.eq(productLabelWhenOutOfStock);

      const valueAvailabilityDate = await boProductsCreateTabStocksPage.getValue(page, 'available_date');
      expect(valueAvailabilityDate).to.eq(todayDate);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, productMinimalQuantity, [], false);

      const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(productMinimalQuantity);

      const productAvailability = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains(productLabelWhenInStock);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBackOffice', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the deny orders option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDenyOrder', baseContext);

      await boProductsCreateTabStocksPage.setQuantityDelta(page, productQuantity * -1);
      await boProductsCreateTabStocksPage.setOptionWhenOutOfStock(page, 'Deny orders');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
      expect(productHeaderSummary.quantity).to.equal('0 out of stock');

      const result = await boProductsCreateTabStocksPage.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${dataEmployees.defaultEmployee.firstName} ${dataEmployees.defaultEmployee.lastName}`),
        expect(result.quantity).to.equal(productQuantity * -1),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryAddProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.be.equal(false);

      const productAvailability = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains('OUT OF STOCK');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBackOffice2', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the allow orders option and set Label when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllowOrder', baseContext);

      await boProductsCreateTabStocksPage.setOptionWhenOutOfStock(page, 'Allow orders');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.be.equal(true);

      const productAvailability = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains(productLabelWhenOutOfStock);
    });
  });

  // 3 - Delete product
  deleteProductTest(newProductData, `${baseContext}_postTest_1`);
});
