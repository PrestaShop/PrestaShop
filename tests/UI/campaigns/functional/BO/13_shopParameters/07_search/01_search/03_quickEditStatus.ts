// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';

// Import data
import Aliases from '@data/demo/search';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

    await searchPage.resetFilter(page);
    await searchPage.filterTable(page, 'input', 'alias', Aliases.bloose.alias);

    const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
    expect(textAlias).to.contains(Aliases.bloose.alias);
  });

  const statuses = [
    {args: {status: 'disable', enable: false}},
    {args: {status: 'enable', enable: true}},
  ];

  statuses.forEach((aliasStatus) => {
    it(`should ${aliasStatus.args.status} status`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${aliasStatus.args.status}Status`, baseContext);

      const isActionPerformed = await searchPage.setStatus(page, 1, aliasStatus.args.enable);

      if (isActionPerformed) {
        const resultMessage = await searchPage.getAlertSuccessBlockContent(page);
        expect(resultMessage).to.contains(searchPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await searchPage.getStatus(page, 1);
      expect(currentStatus).to.be.equal(aliasStatus.args.enable);
    });
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfSearchAfterReset = await searchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearchAfterReset).to.be.equal(numberOfSearch);
  });
});
