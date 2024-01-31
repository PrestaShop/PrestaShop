// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import createProductPage from '@pages/BO/catalog/products/add';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import movementsPage from '@pages/BO/catalog/stocks/movements';

// Import FO pages
import foProductPage from '@pages/FO/classic/product';

// Import data
import Employees from '@data/demo/employees';
import ProductData from '@data/faker/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_stocksTab';

describe('BO - Catalog - Products : Stocks tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newProductData: ProductData = new ProductData({
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
  const todayDate: string = date.getDateFormat('yyyy-mm-dd');

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in Stock tab
  describe('Check all options in Stock tab', async () => {
    it('should go to the Stocks tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksTab', baseContext);

      await createProductPage.goToTab(page, 'stock');

      const isTabActive = await createProductPage.isTabActive(page, 'stock');
      expect(isTabActive).to.eq(true);
    });

    it('should add quantity to stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addQuantityToStock', baseContext);

      await stocksTab.setQuantityDelta(page, productQuantity);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);

      const productHeaderSummary = await createProductPage.getProductHeaderSummary(page);
      expect(productHeaderSummary.quantity).to.equal(`${productQuantity} in stock`);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result = await stocksTab.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${Employees.DefaultEmployee.firstName} ${Employees.DefaultEmployee.lastName}`),
        expect(result.quantity).to.equal(productQuantity),
      ]);
    });

    it('should click on View all stock movements', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickViewAllStockMovements', baseContext);

      page = await stocksTab.clickViewAllStockMovements(page);

      const pageTitle = await movementsPage.getPageTitle(page);
      expect(pageTitle).to.equal(movementsPage.pageTitle);
    });

    it('should close the Stock Movements page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeStockMovementsPage', baseContext);

      page = await movementsPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should fill Stocks values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillStockValues', baseContext);

      await stocksTab.setMinimalQuantity(page, productMinimalQuantity);
      await stocksTab.setStockLocation(page, productStockLocation);
      await stocksTab.setLowStockAlertByEmail(page, productLowStockAlertByEmail, productLowStockThreshold);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check Stocks values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockValues', baseContext);

      const valueMinimalQuantity = await stocksTab.getValue(page, 'minimal_quantity');
      expect(valueMinimalQuantity).to.eq(productMinimalQuantity.toString());

      const valueStockLocation = await stocksTab.getValue(page, 'location');
      expect(valueStockLocation).to.eq(productStockLocation);

      const valueLowStockAlertByEmail = await stocksTab.getValue(page, 'low_stock_threshold_enabled');
      expect(valueLowStockAlertByEmail).to.eq(productLowStockAlertByEmail ? '1' : '0');

      const valueLowStockThreshold = await stocksTab.getValue(page, 'low_stock_threshold');
      expect(valueLowStockThreshold).to.eq(productLowStockThreshold.toString());
    });

    it('should fill When out of stock values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillWhenOutOfStockValues', baseContext);

      await stocksTab.setLabelWhenInStock(page, productLabelWhenInStock);
      await stocksTab.setLabelWhenOutOfStock(page, productLabelWhenOutOfStock);
      await stocksTab.setAvailabilityDate(page, todayDate);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check When out of stock values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWhenOutOfStockValues', baseContext);

      const valueLabelWhenInStock = await stocksTab.getValue(page, 'available_now', '1');
      expect(valueLabelWhenInStock).to.eq(productLabelWhenInStock);

      const valueLabelWhenOutOfStock = await stocksTab.getValue(page, 'available_later', '1');
      expect(valueLabelWhenOutOfStock).to.eq(productLabelWhenOutOfStock);

      const valueAvailabilityDate = await stocksTab.getValue(page, 'available_date');
      expect(valueAvailabilityDate).to.eq(todayDate);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      // Add the product to the cart
      await foProductPage.addProductToTheCart(page, productMinimalQuantity, [], false);

      const notificationsNumber = await foProductPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(productMinimalQuantity);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains(productLabelWhenInStock);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBackOffice', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the deny orders option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDenyOrder', baseContext);

      await stocksTab.setQuantityDelta(page, productQuantity * -1);
      await stocksTab.setOptionWhenOutOfStock(page, 'Deny orders');

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);

      const productHeaderSummary = await createProductPage.getProductHeaderSummary(page);
      expect(productHeaderSummary.quantity).to.equal('0 out of stock');

      const result = await stocksTab.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${Employees.DefaultEmployee.firstName} ${Employees.DefaultEmployee.lastName}`),
        expect(result.quantity).to.equal(productQuantity * -1),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryAddProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.be.equal(false);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains('Out-of-Stock');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBackOffice2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the allow orders option and set Label when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllowOrder', baseContext);

      await stocksTab.setOptionWhenOutOfStock(page, 'Allow orders');

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.be.equal(true);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains(productLabelWhenOutOfStock);
    });
  });

  // 3 - Delete product
  deleteProductTest(newProductData, `${baseContext}_postTest_1`);
});
