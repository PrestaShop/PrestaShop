// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';
import date from '@utils/date';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import productsPage from '@pages/BO/catalog/products';
import createProductPage from '@pages/BO/catalog/products/add';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import movementsPage from '@pages/BO/catalog/stocks/movements';

// Import FO pages
import foProductPage from '@pages/FO/hummingbird/product';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerProduct,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_outOfStockBehaviour';

describe('FO - Product page - Product page : Out of stock behaviour', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const todayDate: string = date.getDateFormat('yyyy-mm-dd');

  // Data to create new product
  const newProductData: FakerProduct = new FakerProduct({
    name: 'test',
    type: 'standard',
    quantity: 300,
    minimumQuantity: 0,
    status: true,
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Check out of stock behaviour', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await createProductPage.closeSfToolBar(page);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should go to the Stocks tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksTab', baseContext);

      await createProductPage.goToTab(page, 'stock');

      const isTabActive = await createProductPage.isTabActive(page, 'stock');
      expect(isTabActive).to.equal(true);
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

      await stocksTab.setMinimalQuantity(page, 5);
      await stocksTab.setStockLocation(page, 'Second floor');
      await stocksTab.setLowStockAlertByEmail(page, true, 3);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should fill When out of stock values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillWhenOutOfStockValues', baseContext);

      await stocksTab.setLabelWhenInStock(page, 'In stock');
      await stocksTab.setLabelWhenOutOfStock(page, 'Out of stock');
      await stocksTab.setAvailabilityDate(page, todayDate);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should preview product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await createProductPage.previewProduct(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the label \'In stock\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLabelInStock', baseContext);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.contains('In stock');
    });

    it('should check the notification of minimum purchase order quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationMinimumPurchase', baseContext);

      const minimumPurchaseLabel = await foProductPage.getMinimalProductQuantityLabel(page);
      expect(minimumPurchaseLabel).to.contains('The minimum purchase order quantity for the product is 5.');
    });

    it('should click on add to cart button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAddToCartButton', baseContext);

      // Add the product to the cart
      await foProductPage.clickOnAddToCartButton(page);

      const notificationsNumber = await foProductPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });

    it('should close the blockCart modal by clicking outside the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal2', baseContext);

      const isQuickViewModalClosed = await blockCartModal.closeBlockCartModal(page, true);
      expect(isQuickViewModalClosed).to.equal(true);
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

      await stocksTab.setQuantityDelta(page, -300);
      await stocksTab.setOptionWhenOutOfStock(page, 'Deny orders');

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryAddProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.equal(false);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.contains('Out-of-Stock');
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

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that the Add to cart Button is enabled and check the availability icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const isAddToCartButtonEnabled = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.be.equal(true);

      const productAvailability = await foProductPage.getProductAvailabilityLabel(page);
      expect(productAvailability).to.be.contains('Out of stock');
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete created product
  deleteProductTest(newProductData, `${baseContext}_postTest_2`);
});
