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

const baseContext: string = 'functional_BO_shopParameters_productSettings_pagination_updateDefaultProductsOrder';

/*
Update default products order to this values :
'Product name - Ascending/Descending', 'Product price - Ascending/Descending', 'Position inside category - Ascending'
And check that order in FO
 */
describe('BO - Shop Parameters - Product Settings : Update default product order', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    expect(pageTitle).to.contains(productSettingsPage.pageTitle);
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

  tests.forEach((test, index: number) => {
    describe(`Set products default order to: '${test.args.orderBy} - ${test.args.orderMethod}'`, async () => {
      it(`should set products default order to: '${test.args.orderBy} - ${test.args.orderMethod}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateProductsOrder${index + 1}`, baseContext);

        const result = await productSettingsPage.setDefaultProductsOrder(
          page,
          test.args.orderBy,
          test.args.orderMethod,
        );

        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index + 1}`, baseContext);

        page = await productSettingsPage.viewMyShop(page);

        const isHomePage = await homePageFO.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToHomeCategory${index + 1}`, baseContext);

        await homePageFO.changeLanguage(page, 'en');
        await homePageFO.goToAllProductsPage(page);

        const isCategoryPage = await categoryPageFO.isCategoryPage(page);
        expect(isCategoryPage, 'Home category page was not opened');
      });

      it(
        `should check that products are ordered by: '${test.args.orderBy} - ${test.args.orderMethod}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductsOrder${index + 1}`, baseContext);

          const defaultProductOrder = await categoryPageFO.getSortByValue(page);
          expect(defaultProductOrder, 'Default products order is incorrect').to.contains(test.args.textOnSelect);
        },
      );

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index + 1}`, baseContext);

        page = await homePageFO.closePage(browserContext, page, 0);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });
    });
  });
});
