// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import combinationsTab from '@pages/BO/catalog/products/add/combinationsTab';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import {foProductPage} from '@pages/FO/classic/product';
import {homePage} from '@pages/FO/classic/home';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_displayUnavailableProductAttributes';

describe('BO - Shop Parameters - Product Settings : Display unavailable product attributes '
  + 'on the product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({
    type: 'combinations',
    attributes: [
      {
        name: 'color',
        values: ['White'],
      },
      {
        name: 'size',
        values: ['S'],
      },
    ],
    quantity: 0,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display unavailable product attributes on the product page', async () => {
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
      expect(isModalVisible).to.be.equal(true);
    });

    it('should choose product with combinations type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseTypeOfProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });

    it('should create combinations and click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      await combinationsTab.setProductAttributes(page, productData.attributes);

      const successMessage = await combinationsTab.generateCombinations(page);
      expect(successMessage).to.equal(combinationsTab.successfulGenerateCombinationsMessage(1));
    });

    it('should close combinations generation modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed2', baseContext);

      const isModalClosed = await combinationsTab.generateCombinationModalIsClosed(page);
      expect(isModalClosed).to.be.equal(true);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.shopParametersParentLink,
        addProductPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    const tests = [
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ];
    tests.forEach((test, index: number) => {
      it(`should ${test.args.action} Display unavailable product attributes on the product page`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.action}DisplayUnavailableProductAttributes`,
          baseContext,
        );

        const result = await productSettingsPage.setDisplayUnavailableProductAttributesStatus(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the created product and go to product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCreatedProductPage${index}`, baseContext);

        await homePage.searchProduct(page, productData.name);
        await searchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(productData.name.toUpperCase());
      });

      it('should check the unavailable product attributes in FO product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUnavailableAttribute${index}`, baseContext);

        const sizeIsVisible = await foProductPage.isUnavailableProductSizeDisplayed(page, productData.attributes[1].values[0]);
        expect(sizeIsVisible).to.be.equal(test.args.enable);

        const colorIsVisible = await foProductPage.isUnavailableProductColorDisplayed(page, productData.attributes[0].values[0]);
        expect(colorIsVisible).to.be.equal(test.args.enable);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await productsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await productsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(productsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await productsPage.resetFilter(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
