// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import addProductPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boProductsPage,
  boProductsCreateTabDescriptionPage,
  boShopParametersPage,
  dataProducts,
  foClassicProductPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_general_general_allowIframes';

/*
Enable/Disable allow iframe
Go to product page and edit the description
Add an iframe in the description
Preview product and check the product description
 */
describe('BO - Shop Parameters - General : Enable/Disable Allow iframes on HTML field', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const description: string = '<iframe width="560" height="315" src="https://www.youtube.com/embed'
    + '/3qcApq8NMhw?si=0O8BBWjbJ7gJRkoi" title="YouTube video player" frameborder="0" allow="accelerometer; '
    + 'autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  const tests = [
    {args: {action: 'Disable', exist: false}},
    {args: {action: 'Enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Allow iframes on HTML fields`, async () => {
      it('should go to \'Shop parameters > General\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.shopParametersGeneralLink,
        );
        await boShopParametersPage.closeSfToolBar(page);

        const pageTitle = await boShopParametersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
      });

      it(`should ${test.args.action} allow iframes`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AllowIframes`, baseContext);

        const result = await boShopParametersPage.setAllowIframes(page, test.args.exist);
        expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
      });

      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should go to first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should add an iframe in the product description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `editDescription${index}`, baseContext);

        await boProductsCreateTabDescriptionPage.setIframeInDescription(page, description);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/33921
        // To delete after the fix of the issue
        if (test.args.action === 'Disable') {
          await addProductPage.clickOnSaveProductButton(page);
        } else {
          const message = await addProductPage.saveProduct(page);
          expect(message).to.eq(addProductPage.successfulUpdateMessage);
        }
      });

      it('should preview the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `previewProduct${index}`, baseContext);

        page = await addProductPage.previewProduct(page);
        await foClassicProductPage.changeLanguage(page, 'en');

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(dataProducts.demo_14.name);
      });

      it('should check the existence of the iframe in the product description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkIframe${index}`, baseContext);

        const isIframeVisible = await foClassicProductPage.isIframeVisibleInProductDescription(page);
        expect(isIframeVisible).to.equal(test.args.exist);

        if (test.args.exist) {
          const youtubeURL = await foClassicProductPage.getURLInProductDescription(page);
          expect(youtubeURL).to.equal('https://www.youtube.com/embed/3qcApq8NMhw?si=0O8BBWjbJ7gJRkoi');
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });
  });

  // POST-TEST : Delete iframe in product description
  describe('POST-TEST : Reset product description', async () => {
    it('should go to Products page', async function () {
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

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await boProductsPage.goToProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should reset the product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetDescription', baseContext);

      await boProductsCreateTabDescriptionPage.setIframeInDescription(page, '');
      await boProductsCreateTabDescriptionPage.setDescription(page, dataProducts.demo_14.description);

      const message = await addProductPage.saveProduct(page);
      expect(message).to.eq(addProductPage.successfulUpdateMessage);
    });
  });
});
