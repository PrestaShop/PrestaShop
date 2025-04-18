import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const productData: FakerProduct = new FakerProduct({type: 'standard', quantity: 2});
  const remainingQuantity: number = 0;
  const defaultRemainingQuantity: number = 3;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display remaining quantities', async () => {
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
      expect(isModalVisible).to.be.equal(true);
    });

    it('should check the standard product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await boProductsCreatePage.goToSubMenu(
        page,
        boProductsCreatePage.shopParametersParentLink,
        boProductsCreatePage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    [
      {quantity: remainingQuantity, exist: false, state: 'Displayed'},
      {quantity: defaultRemainingQuantity, exist: true, state: 'NotDisplayed'},
    ].forEach((test, index: number) => {
      it(`should update Display remaining quantities to ${test.quantity}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDisplayRemainingQuantity${index}`, baseContext);

        const result = await boProductSettingsPage.setDisplayRemainingQuantities(page, test.quantity);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${test.state}`, baseContext);

        page = await boProductSettingsPage.viewMyShop(page);

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the product and go to product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${test.state}`, baseContext);

        await foClassicHomePage.searchProduct(page, productData.name);
        await foClassicSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should check the product availability', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkThatRemainingQuantityIs${test.state}`,
          baseContext,
        );

        const lastQuantityIsVisible = await foClassicProductPage.isAvailabilityQuantityDisplayed(page);
        expect(lastQuantityIsVisible).to.be.equal(test.exist);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.state}`, baseContext);

        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });
  });
});
