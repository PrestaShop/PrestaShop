require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');

// Import FO pages
const homePageFO = require('@pages/FO/home');
const categoryPageFO = require('@pages/FO/category');

const baseContext = 'functional_BO_shopParameters_productSettings_pagination_updateNumberOfProductsPerPage';

let browserContext;
let page;
const updatedProductPerPage = 5;
const defaultNumberOfProductsPerPage = 10;

/*
Set number of products displayed to 5
Check the update in FO
Set number of products displayed to default value 10
Check the update in FO
 */
describe('BO - Shop Parameters - Product Settings : Update number of product displayed on FO', async () => {
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

  tests.forEach((test, index) => {
    describe(`Update number of product displayed to ${test.args.numberOfProductsPerPage}`, async () => {
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
