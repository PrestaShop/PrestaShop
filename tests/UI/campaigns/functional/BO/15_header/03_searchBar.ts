import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSearchResultsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_searchBar';

describe('BO - Header : Search bar', async () => {
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

  it('should search for "orders"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForOrders', baseContext);

    await boDashboardPage.search(page, 'orders');

    const pageTitle = await boSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchResultsPage.pageTitle);
  });

  it('should check results for "orders"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForOrders', baseContext);

    const numberResults = await boSearchResultsPage.getNumberResults(page);
    expect(numberResults).to.be.eq(3);

    const numberFeatures = await boSearchResultsPage.getNumberResults(page, 'features');
    expect(numberFeatures).to.be.eq(1);

    const numberModules = await boSearchResultsPage.getNumberResults(page, 'modules');
    expect(numberModules).to.be.eq(2);

    const numberLinks = await boSearchResultsPage.getSearchPanelsLinksNumber(page);
    expect(numberLinks).to.be.at.least(1);

    const linkHref = await boSearchResultsPage.getSearchPanelsLinkURL(page, 1);
    expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await boSearchResultsPage.getSearchPanelsLinkText(page, 1);
    expect(linkText).to.contains('Go to the documentation');
  });

  it('should search for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForJohnDoe', baseContext);

    await boSearchResultsPage.search(page, 'John Doe');

    const pageTitle = await boSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchResultsPage.pageTitle);
  });

  it('should check results for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForJohnDoe', baseContext);

    const numberResults = await boSearchResultsPage.getNumberResults(page);
    expect(numberResults).to.be.eq(1);

    const numberCustomers = await boSearchResultsPage.getNumberResults(page, 'customers');
    expect(numberCustomers).to.be.eq(1);

    const customerFirstName = await boSearchResultsPage.getTextColumn(page, 'customers', 1, 'firstname');
    expect(customerFirstName).to.be.eq('John');

    const customerName = await boSearchResultsPage.getTextColumn(page, 'customers', 1, 'name');
    expect(customerName).to.be.eq('DOE');

    const numberLinks = await boSearchResultsPage.getSearchPanelsLinksNumber(page);
    expect(numberLinks).to.be.at.least(1);

    const linkHref = await boSearchResultsPage.getSearchPanelsLinkURL(page, 1);
    expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await boSearchResultsPage.getSearchPanelsLinkText(page, 1);
    expect(linkText).to.contains('Go to the documentation');
  });
});
