import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopPage from '@pages/BO/advancedParameters/multistore/shop/add';
import shopPage from '@pages/BO/advancedParameters/multistore/shop';
import addShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';
import createProductPage from '@pages/BO/catalog/productsV2/add';
import productsPage from '@pages/BO/catalog/productsV2';

// Import FO pages
import {homePage} from '@pages/FO/home';

// Import data
import ProductData from '@data/faker/product';
import ShopData from '@data/faker/shop';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'productV2_functional_multistore';

describe('BO - Catalog - Products : Multistore', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createShopData: ShopData = new ShopData({name: 'newShop', shopGroup: 'Default', categoryRoot: 'Home'});

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  const editProductData: ProductData = new ProductData({
    type: 'standard',
    applyChangesToAllStores: true,
    quantity: 100,
    status: true,
  });

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create new store and set URL', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );
      await multiStorePage.closeSfToolBar(page);

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await addShopPage.setShop(page, createShopData);
      expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopPage.filterTable(page, 'a!name', createShopData.name);
      await shopPage.goToSetURL(page, 1);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });
  });

  describe('Check multistore', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on multistore header and select the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMultistoreHeader', baseContext);

      await productsPage.clickOnMultiStoreHeader(page);
      await productsPage.chooseShop(page, 2);

      const shopName = await productsPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);

      const shopColor = await productsPage.getShopColor(page);
      expect(shopColor).to.contains(createShopData.color);
    });

    it('should click on view my store', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyStore', baseContext);

      page = await productsPage.viewMyStore(page);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.contains(homePage.pageTitle);

      const newUrl = await homePage.getCurrentURL(page);
      expect(newUrl).to.contains(createShopData.name);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });
  });

  describe('Create new product', async () => {
    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should click on \'Select stores\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSelectStoresButton', baseContext);

      // @toDo https://github.com/PrestaShop/PrestaShop/issues/34197
    });

    it('should update product name and click on apply changes to all stores', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'applyChangesToAllStores', baseContext);

      await createProductPage.setProductName(page, editProductData.name);
      await createProductPage.applyChangesToAllStores(page, editProductData.applyChangesToAllStores);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should go to catalog page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await createProductPage.goToCatalogPage(page);

      const productName = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.eq(editProductData.name);
    });

    it('should click on multistore header and select the default shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMultistoreHeader2', baseContext);

      await productsPage.clickOnMultiStoreHeader(page);
      await productsPage.chooseShop(page, 1);

      const shopName = await productsPage.getShopName(page);
      expect(shopName).to.eq(global.INSTALL.SHOP_NAME);

      const shopColor = await productsPage.getShopColor(page);
      expect(shopColor).to.eq('');
    });

    it.skip('should check the updated product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

      const productName = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.eq(editProductData.name);
    });

    it('should select the created shop and delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMultistoreHeader3', baseContext);

      // To delete when https://github.com/PrestaShop/PrestaShop/issues/34197 is fixed
      await productsPage.clickOnMultiStoreHeader(page);
      await productsPage.chooseShop(page, 2);

      const shopName = await productsPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const isModalVisible = await productsPage.clickOnDeleteProductFromStoreButton(page, 1);
      expect(isModalVisible).to.equal(true);

      const textMessage = await productsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
