// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boProductsPage,
  type BrowserContext,
  dataModules,
  FakerProduct,
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_installation_disableEnableModule';

describe('Mail alerts module - Disable/Enable module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number;
  let nthProduct: number | null;

  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
    name: 'Product Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 0,
    behaviourOutOfStock: 'Deny orders',
  });

  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest_0`);

  describe('Disable/Enable module', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    describe('BackOffice - Fetch the ID of the product', async () => {
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

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter list by \'product_name\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterProductName', baseContext);

        await boProductsPage.filterProducts(page, 'product_name', productOutOfStockNotAllowed.name, 'input');

        const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);

        idProduct = await boProductsPage.getTextColumn(page, 'id_product', 1) as number;
      });
    });

    [
      {
        state: false,
        action: 'disable',
      },
      {
        state: true,
        action: 'enable',
      },
    ].forEach((test: {state: boolean, action: string}, index: number) => {
      describe(`${utilsCore.capitalize(test.action)} the module`, async () => {
        it('should go to \'Modules > Module Manager\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToModuleManagerPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.modulesParentLink,
            boDashboardPage.moduleManagerLink,
          );
          await boModuleManagerPage.closeSfToolBar(page);

          const pageTitle = await boModuleManagerPage.getPageTitle(page);
          expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
        });

        it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchModule${index}`, baseContext);

          const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
          expect(isModuleVisible).to.eq(true);
        });

        it(`should ${test.action} the module`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.action}Module`, baseContext);

          const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psEmailAlerts, test.action);

          if (test.state) {
            expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psEmailAlerts.tag));
          } else {
            expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psEmailAlerts.tag));
          }
        });

        it('should go to Front Office', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFo${index}`, baseContext);

          page = await boModuleManagerPage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should go to the category Page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCategoryPage${index}`, baseContext);

          await foClassicHomePage.goToAllProductsPage(page);

          const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
          expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
        });

        it('should go to the next page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNextCategoryPage${index}`, baseContext);

          await foClassicCategoryPage.goToNextPage(page);

          nthProduct = await foClassicCategoryPage.getNThChildFromIDProduct(page, idProduct);
          expect(nthProduct).to.not.eq(null);
        });

        it('should go to the product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

          await foClassicCategoryPage.goToProductPage(page, nthProduct!);

          const pageTitle = await foClassicProductPage.getPageTitle(page);
          expect(pageTitle.toUpperCase()).to.contains(productOutOfStockNotAllowed.name.toUpperCase());

          const hasFlagOutOfStock = await foClassicProductPage.hasProductFlag(page, 'out_of_stock');
          expect(hasFlagOutOfStock).to.be.equal(true);

          const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
          expect(hasBlockMailAlert).to.be.equal(test.state);
        });

        it('should close the page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `closePage${index}`, baseContext);

          page = await foClassicCategoryPage.closePage(browserContext, page);

          const pageTitle = await boModuleManagerPage.getPageTitle(page);
          expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
        });
      });
    });
  });

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);
});
