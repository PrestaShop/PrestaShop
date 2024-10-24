// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  foClassicCategoryPage,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.productSettingsLink,
    );

    await boProductSettingsPage.closeSfToolBar(page);

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
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

        const result = await boProductSettingsPage.setDefaultProductsOrder(
          page,
          test.args.orderBy,
          test.args.orderMethod,
        );

        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index + 1}`, baseContext);

        page = await boProductSettingsPage.viewMyShop(page);

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToHomeCategory${index + 1}`, baseContext);

        await foClassicHomePage.changeLanguage(page, 'en');
        await foClassicHomePage.goToAllProductsPage(page);

        const isCategoryPage = await foClassicCategoryPage.isCategoryPage(page);
        expect(isCategoryPage, 'Home category page was not opened');
      });

      it(
        `should check that products are ordered by: '${test.args.orderBy} - ${test.args.orderMethod}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductsOrder${index + 1}`, baseContext);

          const defaultProductOrder = await foClassicCategoryPage.getSortByValue(page);
          expect(defaultProductOrder, 'Default products order is incorrect').to.contains(test.args.textOnSelect);
        },
      );

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index + 1}`, baseContext);

        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });
  });
});
