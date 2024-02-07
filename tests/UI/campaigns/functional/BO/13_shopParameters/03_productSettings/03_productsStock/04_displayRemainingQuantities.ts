// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {foProductPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_displayRemainingQuantities';

/*
Create product quantity 2
Update display remaining quantities to 0
Go to FO product page and check that the product availability is not displayed
Update display remaining quantities to the default value
Go to FO product page and check that the product availability is displayed
 */
describe('BO - Shop Parameters - Product Settings : Display remaining quantities', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({type: 'standard', quantity: 2});
  const remainingQuantity: number = 0;
  const defaultRemainingQuantity: number = 3;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Display remaining quantities', async () => {
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

    it('should check the standard product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
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

    [
      {quantity: remainingQuantity, exist: false, state: 'Displayed'},
      {quantity: defaultRemainingQuantity, exist: true, state: 'NotDisplayed'},
    ].forEach((test, index: number) => {
      it(`should update Display remaining quantities to ${test.quantity}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDisplayRemainingQuantity${index}`, baseContext);

        const result = await productSettingsPage.setDisplayRemainingQuantities(page, test.quantity);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${test.state}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the product and go to product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${test.state}`, baseContext);

        await homePage.searchProduct(page, productData.name);
        await searchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should check the product availability', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkThatRemainingQuantityIs${test.state}`,
          baseContext,
        );

        const lastQuantityIsVisible = await foProductPage.isAvailabilityQuantityDisplayed(page);
        expect(lastQuantityIsVisible).to.be.equal(test.exist);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.state}`, baseContext);

        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });
  });
});
