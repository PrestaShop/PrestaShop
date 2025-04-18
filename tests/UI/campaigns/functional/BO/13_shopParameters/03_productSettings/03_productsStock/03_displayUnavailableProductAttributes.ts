import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_displayUnavailableProductAttributes';

describe('BO - Shop Parameters - Product Settings : Display unavailable product attributes '
  + 'on the product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Display unavailable product attributes on the product page', async () => {
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

    it('should choose product with combinations type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseTypeOfProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);

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
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should create combinations and click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      await boProductsCreateTabCombinationsPage.setProductAttributes(page, productData.attributes);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(1));
    });

    it('should close combinations generation modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed2', baseContext);

      const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
      expect(isModalClosed).to.be.equal(true);
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

        const result = await boProductSettingsPage.setDisplayUnavailableProductAttributesStatus(page, test.args.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await boProductSettingsPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the created product and go to product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCreatedProductPage${index}`, baseContext);

        await foClassicHomePage.searchProduct(page, productData.name);
        await foClassicSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle.toUpperCase()).to.contains(productData.name.toUpperCase());
      });

      it('should check the unavailable product attributes in FO product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUnavailableAttribute${index}`, baseContext);

        const sizeIsVisible = await foClassicProductPage.isUnavailableProductSizeDisplayed(
          page,
          productData.attributes[1].values[0],
        );
        expect(sizeIsVisible).to.be.equal(test.args.enable);

        const colorIsVisible = await foClassicProductPage.isUnavailableProductColorDisplayed(
          page,
          productData.attributes[0].values[0],
        );
        expect(colorIsVisible).to.be.equal(test.args.enable);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await boProductSettingsPage.goToSubMenu(
        page,
        boProductSettingsPage.catalogParentLink,
        boProductSettingsPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await boProductsPage.resetFilter(page);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
