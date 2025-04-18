import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsCreatePage,
  boProductsPage,
  boSearchPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

const baseContext: string = 'functional_BO_shopParameters_search_search_indexing';

describe('BO - Shop Parameters - Search : Indexing', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numIndexedProducts: number = 0;

  const product1: FakerProduct = new FakerProduct({status: true});
  const product2: FakerProduct = new FakerProduct({status: true});
  const product3: FakerProduct = new FakerProduct({status: true});

  describe('Indexing', async () => {
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

    it('should go to \'Shop Parameters > Search\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.searchLink,
      );

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);
    });

    it('should check Indexed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIndexedProducts', baseContext);

      numIndexedProducts = await boSearchPage.getNumIndexedProducts(page);
      expect(numIndexedProducts).to.be.gt(0);

      const numTotalProducts = await boSearchPage.getNumTotalProducts(page);
      expect(numIndexedProducts).to.equal(numTotalProducts);
    });
    [
      {
        status: 'enable',
        product: product2,
        numProductsIndexed: 1,
        numProductsTotal: 1,
      },
      {
        status: 'disable',
        product: product1,
        numProductsIndexed: 1,
        numProductsTotal: 2,
      },
    ].forEach((
      arg: {status: string, product: FakerProduct, numProductsIndexed: number, numProductsTotal: number},
      index: number,
    ) => {
      it(`should ${arg.status} Indexing`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.status}${index}`, baseContext);

        const textResult = await boSearchPage.setIndexing(page, arg.status === 'enable');
        expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.eq(true);
      });

      it(`should choose '${arg.product.type} product'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `chooseTypeOfProduct${index}`, baseContext);

        await boProductsPage.selectProductType(page, arg.product.type);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${index}`, baseContext);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setProduct${index}`, baseContext);

        await boProductsCreatePage.closeSfToolBar(page);

        const createProductMessage = await boProductsCreatePage.setProduct(page, arg.product);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should go to \'Shop Parameters > Search\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToSearchPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.searchLink,
        );

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });

      it('should check Indexed products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkIndexedProductsAfterAddProduct${index}`, baseContext);

        const numIndexedProductsAfterAdd = await boSearchPage.getNumIndexedProducts(page);
        expect(numIndexedProductsAfterAdd).to.be.gt(0);
        expect(numIndexedProductsAfterAdd).to.equal(numIndexedProducts + arg.numProductsIndexed);

        const numTotalProducts = await boSearchPage.getNumTotalProducts(page);
        expect(numTotalProducts).to.equal(numIndexedProducts + arg.numProductsTotal);
      });
    });
    // Add missing products to the index

    it('should click on "Add missing products to the index"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddMissingProductsIndex', baseContext);

      const result = await boSearchPage.clickAddMissingProductsToIndex(page);
      expect(result).to.contains(boSearchPage.successfulUpdateMessage);
    });

    it('should check Indexed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIndexedProductsAfterReindexing', baseContext);

      const numIndexedProductsAfterAdd = await boSearchPage.getNumIndexedProducts(page);
      expect(numIndexedProductsAfterAdd).to.be.gt(0);
      expect(numIndexedProductsAfterAdd).to.equal(numIndexedProducts + 2);

      const numTotalProducts = await boSearchPage.getNumTotalProducts(page);
      expect(numTotalProducts).to.equal(numIndexedProductsAfterAdd);
      expect(numTotalProducts).to.equal(numIndexedProducts + 2);
    });

    // Re-build the entire index
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);
    });

    it(`should choose '${product3.type} product'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseTypeOfProduct', baseContext);

      await boProductsPage.selectProductType(page, product3.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, product3);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to \'Shop Parameters > Search\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSearchPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.searchLink,
      );

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);
    });

    it('should check Indexed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIndexedProductsAfterAddProduct', baseContext);

      const numIndexedProductsAfterAdd = await boSearchPage.getNumIndexedProducts(page);
      expect(numIndexedProductsAfterAdd).to.be.gt(0);
      expect(numIndexedProductsAfterAdd).to.equal(numIndexedProducts + 2);

      const numTotalProducts = await boSearchPage.getNumTotalProducts(page);
      expect(numTotalProducts).to.equal(numIndexedProducts + 3);
    });

    it('should click on "Re-build the entire index"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickRebuildEntireIndex', baseContext);

      const result = await boSearchPage.clickRebuildEntireIndex(page);
      expect(result).to.contains(boSearchPage.successfulUpdateMessage);
    });

    it('should check Indexed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIndexedProductsAfterRebuildEntireIndex', baseContext);

      const numIndexedProductsAfterAdd = await boSearchPage.getNumIndexedProducts(page);
      expect(numIndexedProductsAfterAdd).to.be.gt(0);
      expect(numIndexedProductsAfterAdd).to.equal(numIndexedProducts + 3);

      const numTotalProducts = await boSearchPage.getNumTotalProducts(page);
      expect(numTotalProducts).to.equal(numIndexedProducts + 3);
    });

    // Reset
    it('should enable Indexing', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetIndexing', baseContext);

      const textResult = await boSearchPage.setIndexing(page, true);
      expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
    });
  });

  // POST-TEST : Delete products
  deleteProductTest(product1, `${baseContext}_post_0`);
  deleteProductTest(product2, `${baseContext}_post_1`);
  deleteProductTest(product3, `${baseContext}_post_2`);
});
