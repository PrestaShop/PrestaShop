// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
// Import FO pages
import {foProductPage} from '@pages/FO/classic/product';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
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

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.shopParametersGeneralLink,
        );
        await generalPage.closeSfToolBar(page);

        const pageTitle = await generalPage.getPageTitle(page);
        expect(pageTitle).to.contains(generalPage.pageTitle);
      });

      it(`should ${test.args.action} allow iframes`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AllowIframes`, baseContext);

        const result = await generalPage.setAllowIframes(page, test.args.exist);
        expect(result).to.contains(generalPage.successfulUpdateMessage);
      });

      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should go to first product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should add an iframe in the product description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `editDescription${index}`, baseContext);

        await descriptionTab.setIframeInDescription(page, description);

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
        await foProductPage.changeLanguage(page, 'en');

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(Products.demo_14.name);
      });

      it('should check the existence of the iframe in the product description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkIframe${index}`, baseContext);

        const isIframeVisible = await foProductPage.isIframeVisibleInProductDescription(page);
        expect(isIframeVisible).to.equal(test.args.exist);

        if (test.args.exist) {
          const youtubeURL = await foProductPage.getURLInProductDescription(page);
          expect(youtubeURL).to.equal('https://www.youtube.com/embed/3qcApq8NMhw?si=0O8BBWjbJ7gJRkoi');
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foProductPage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });
  });

  // POST-TEST : Delete iframe in product description
  describe('POST-TEST : Reset product description', async () => {
    it('should go to Products page', async function () {
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

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage', baseContext);

      await productsPage.goToProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should reset the product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetDescription', baseContext);

      await descriptionTab.setIframeInDescription(page, '');
      await descriptionTab.setDescription(page, Products.demo_14.description);

      const message = await addProductPage.saveProduct(page);
      expect(message).to.eq(addProductPage.successfulUpdateMessage);
    });
  });
});
