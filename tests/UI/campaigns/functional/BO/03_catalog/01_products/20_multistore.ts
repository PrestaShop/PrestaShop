import testContext from '@utils/testContext';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  FakerProduct,
  FakerShop,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_multistore';

describe('BO - Catalog - Products : Multistore', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createShopData: FakerShop = new FakerShop({name: 'newShop', shopGroup: 'Default', categoryRoot: 'Home'});
  let shopID: number = 0;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  const editProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    applyChangesToAllStores: true,
    quantity: 100,
    status: true,
  });

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new store and set URL', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, createShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  describe('Check multistore', async () => {
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

    it('should click on multistore header and select the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMultistoreHeader', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 2);

      const shopName = await boProductsPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);

      const shopColor = await boProductsPage.getShopColor(page);
      expect(shopColor).to.contains(createShopData.color);
    });

    it('should click on view my store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyStore', baseContext);

      page = await boProductsPage.viewMyStore(page);

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);

      const newUrl = await foClassicHomePage.getCurrentURL(page);
      expect(newUrl).to.contains(createShopData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await foClassicHomePage.closePage(browserContext, page, 0);

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

    it('should go back to catalog page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCatalogPage', baseContext);

      await boProductsCreatePage.goToCatalogPage(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on multistore header and select the default shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectDefaultShop', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 1);

      const shopName = await boProductsPage.getShopName(page);
      expect(shopName).to.eq(global.INSTALL.SHOP_NAME);
    });

    it('should search for the created product and check that the product is not visible in the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedProduct', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', newProductData.name, 'input');

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.eq(0);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfter', baseContext);

      const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.be.gt(0);
    });

    it('should click on multistore header and select the created shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCreatedShop', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 2);

      const shopName = await boProductsPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedProductPage', baseContext);

      await boProductsPage.goToProductPage(page, 1);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on \'Select stores\' button and select the default store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSelectStoresButton', baseContext);

      await boProductsCreatePage.selectStores(page, 1);
    });

    it('should update product name and click on apply changes to all stores', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'applyChangesToAllStores', baseContext);

      await boProductsCreatePage.setProductName(page, editProductData.name);
      await boProductsCreatePage.applyChangesToAllStores(page, editProductData.applyChangesToAllStores);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to catalog page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await boProductsCreatePage.goToCatalogPage(page);

      const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.eq(editProductData.name);
    });

    it('should click on multistore header and select the default shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMultistoreHeader2', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 1);

      const shopName = await boProductsPage.getShopName(page);
      expect(shopName).to.eq(global.INSTALL.SHOP_NAME);

      const shopColor = await boProductsPage.getShopColor(page);
      expect(shopColor).to.eq('');
    });

    it('should check the updated product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

      const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.eq(editProductData.name);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const isModalVisible = await boProductsPage.clickOnDeleteProductFromStoreButton(page, 1);
      expect(isModalVisible).to.equal(true);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  describe('Delete shop', async () => {
    it('should go back to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);

      const pageTitle = await boMultistoreShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(createShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
