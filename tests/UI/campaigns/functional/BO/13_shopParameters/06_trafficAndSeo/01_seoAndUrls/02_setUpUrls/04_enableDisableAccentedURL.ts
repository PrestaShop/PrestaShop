import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabSEOPage,
  boSeoUrlsPage,
  type BrowserContext,
  FakerProduct,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_setUpUrls_enableDisableAccentedURL';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable accented URL', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productName: string = 'TESTURLÉ';
  const productNameWithoutAccent: string = 'TESTURLE';
  const productData: FakerProduct = new FakerProduct({name: productName, type: 'standard'});

  // before and after functions
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

  describe('Enable/Disable accented URL', async () => {
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

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

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

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    const tests = [
      {args: {action: 'enable', enable: true, productNameInURL: productName}},
      {args: {action: 'disable', enable: false, productNameInURL: productNameWithoutAccent}},
    ];

    tests.forEach((test) => {
      it('should go to \'Shop Parameters > SEO & Urls\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToSeoPageTo${test.args.action}`, baseContext);

        await boProductsCreatePage.goToSubMenu(
          page,
          boProductsCreatePage.shopParametersParentLink,
          boProductsCreatePage.trafficAndSeoLink,
        );

        const pageTitle = await boSeoUrlsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
      });

      it(`should ${test.args.action} accented URL`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AccentedUrl`, baseContext);

        const result = await boSeoUrlsPage.enableDisableAccentedURL(page, test.args.enable);
        expect(result).to.contains(boSeoUrlsPage.successfulSettingsUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfter${test.args.action}`, baseContext);

        await boSeoUrlsPage.goToSubMenu(
          page,
          boSeoUrlsPage.catalogParentLink,
          boSeoUrlsPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfter${test.args.action}`, baseContext);

        await boProductsPage.resetFilter(page);

        const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      it('should filter by the created product name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterProductTo${test.args.action}`, baseContext);

        await boProductsPage.filterProducts(page, 'product_name', productName);

        const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(textColumn).to.contains(productName);
      });

      it('should go to the created product page and reset the friendly url', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFriendlyURl${test.args.action}`, baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);

        await boProductsCreatePage.goToTab(page, 'seo');
        await boProductsCreateTabSEOPage.clickOnGenerateUrlFromNameButton(page);

        const updateProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should check the product URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductUrl${test.args.action}`, baseContext);

        // Go to product page in FO
        page = await boProductsCreatePage.previewProduct(page);

        const url = await foClassicHomePage.getCurrentURL(page);
        expect(url).to.contains(test.args.productNameInURL.toLowerCase());
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.args.action}`, baseContext);

        // Go back to BO
        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await boProductsCreatePage.deleteProduct(page);
      expect(testResult).to.equal(boProductsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
