// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import {homePage as homePageFO} from '@pages/FO/home';
import categoryPageFO from '@pages/FO/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_pagination_updateNumberOfProductsPerPage';

/*
Set number of products displayed to 5
Check the update in FO
Set number of products displayed to default value 10
Check the update in FO
 */
describe('BO - Shop Parameters - Product Settings : Update number of product displayed on FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const updatedProductPerPage: number = 5;
  const defaultNumberOfProductsPerPage: number = 10;

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
    {args: {numberOfProductsPerPage: updatedProductPerPage}},
    {args: {numberOfProductsPerPage: defaultNumberOfProductsPerPage}},
  ];

  tests.forEach((test, index: number) => {
    describe(`Update number of product displayed to ${test.args.numberOfProductsPerPage}`, async () => {
      if (index === 0) {
        it('should go to \'Shop parameters > Product Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index + 1}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.productSettingsLink,
          );
          await productSettingsPage.closeSfToolBar(page);

          const pageTitle = await productSettingsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
        });
      }

      it(
        `should set number of products displayed per page to '${test.args.numberOfProductsPerPage}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateProductsPerPage${index + 1}`, baseContext);

          const result = await productSettingsPage.setProductsDisplayedPerPage(
            page,
            test.args.numberOfProductsPerPage,
          );
          await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
        },
      );

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index + 1}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);

        const isHomePage = await homePageFO.isHomePage(page);
        await expect(isHomePage, 'Home page was not opened').to.be.true;
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToHomeCategory${index + 1}`, baseContext);

        await homePageFO.changeLanguage(page, 'en');
        await homePageFO.goToAllProductsPage(page);

        const isCategoryPage = await categoryPageFO.isCategoryPage(page);
        await expect(isCategoryPage, 'Home category page was not opened');
      });

      it(`should check that number of products is equal to '${test.args.numberOfProductsPerPage}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProduct${index + 1}`, baseContext);

        const numberOfProducts = await categoryPageFO.getNumberOfProductsDisplayed(page);

        await expect(
          numberOfProducts,
          'Number of product displayed is incorrect',
        ).to.equal(test.args.numberOfProductsPerPage);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index + 1}`, baseContext);

        page = await homePageFO.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });
  });
});
