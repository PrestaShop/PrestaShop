require('module-alias/register');

const {expect} = require('chai');
// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const homePageFO = require('@pages/FO/home');
const categoryPageFO = require('@pages/FO/category');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_pagination_updateDefaultProductsOrder';

let browserContext;
let page;

/*
Update default products order to this values :
'Product name - Ascending/Descending', 'Product price - Ascending/Descending', 'Position inside category - Ascending'
And check that order in FO
 */
describe('Update default product order', async () => {
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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {
      args:
        {
          orderBy: 'Product name',
          orderMethod: 'Ascending',
          textOnSelect: 'Name, A to Z',
        },
    },
    {
      args:
        {
          orderBy: 'Product name',
          orderMethod: 'Descending',
          textOnSelect: 'Name, Z to A',
        },
    },
    {
      args:
        {
          orderBy: 'Product price',
          orderMethod: 'Ascending',
          textOnSelect: 'Price, low to high',
        },
    },
    {
      args:
        {
          orderBy: 'Product price',
          orderMethod: 'Descending',
          textOnSelect: 'Price, high to low',
        },
    },
    {
      args:
        {
          orderBy: 'Position inside category',
          orderMethod: 'Ascending',
          textOnSelect: 'Relevance',
        },
    },
  ];

  tests.forEach((test, index) => {
    describe(`Set products default order to: '${test.args.orderBy} - ${test.args.orderMethod}'`, async () => {
      it(`should set products default order to: '${test.args.orderBy} - ${test.args.orderMethod}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateProductsOrder${index + 1}`, baseContext);

        const result = await productSettingsPage.setDefaultProductsOrder(
          page,
          test.args.orderBy,
          test.args.orderMethod,
        );

        await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

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

      it(
        `should check that products are ordered by: '${test.args.orderBy} - ${test.args.orderMethod}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductsOrder${index + 1}`, baseContext);

          const defaultProductOrder = await categoryPageFO.getSortByValue(page);
          await expect(defaultProductOrder, 'Default products order is incorrect').to.contains(test.args.textOnSelect);
        },
      );

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index + 1}`, baseContext);

        page = await homePageFO.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });
  });
});
