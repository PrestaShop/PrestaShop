// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  type BrowserContext,
  dataSearchAliases,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_quickEditStatus';

/*
Quick edit status
 */
describe('BO - Shop Parameters - Search : Quick edit status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

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

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await boSearchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

    await boSearchPage.resetFilter(page);
    await boSearchPage.filterTable(page, 'input', 'alias', dataSearchAliases.bloose.alias);

    const textAlias = await boSearchPage.getTextColumn(page, 1, 'alias');
    expect(textAlias).to.contains(dataSearchAliases.bloose.alias);
  });

  const statuses = [
    {args: {status: 'disable', enable: false}},
    {args: {status: 'enable', enable: true}},
  ];

  statuses.forEach((aliasStatus) => {
    it(`should ${aliasStatus.args.status} status`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${aliasStatus.args.status}Status`, baseContext);

      const isActionPerformed = await boSearchPage.setStatus(page, 1, aliasStatus.args.enable);

      if (isActionPerformed) {
        const resultMessage = await boSearchPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(boSearchPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await boSearchPage.getStatus(page, 1);
      expect(currentStatus).to.be.equal(aliasStatus.args.enable);
    });
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfSearchAfterReset = await boSearchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearchAfterReset).to.be.equal(numberOfSearch);
  });
});
