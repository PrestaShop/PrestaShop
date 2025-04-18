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

  const tests = [
    {args: {numberOfProductsPerPage: updatedProductPerPage}},
    {args: {numberOfProductsPerPage: defaultNumberOfProductsPerPage}},
  ];

  tests.forEach((test, index: number) => {
    describe(`Update number of product displayed to ${test.args.numberOfProductsPerPage}`, async () => {
      if (index === 0) {
        it('should go to \'Shop parameters > Product Settings\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index + 1}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.productSettingsLink,
          );
          await boProductSettingsPage.closeSfToolBar(page);

          const pageTitle = await boProductSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
        });
      }

      it(
        `should set number of products displayed per page to '${test.args.numberOfProductsPerPage}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateProductsPerPage${index + 1}`, baseContext);

          const result = await boProductSettingsPage.setProductsDisplayedPerPage(
            page,
            test.args.numberOfProductsPerPage,
          );
          expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
        },
      );

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

      it(`should check that number of products is equal to '${test.args.numberOfProductsPerPage}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProduct${index + 1}`, baseContext);

        const numberOfProducts = await foClassicCategoryPage.getNumberOfProductsDisplayed(page);

        expect(
          numberOfProducts,
          'Number of product displayed is incorrect',
        ).to.equal(test.args.numberOfProductsPerPage);
      });

      it('should close the page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index + 1}`, baseContext);

        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });
  });
});
