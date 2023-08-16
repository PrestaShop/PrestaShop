// Import utils
import helper from '@utils/helpers';
import loginCommon from '@commonTests/BO/loginBO';
import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import test context
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchResultsPage from '@pages/BO/searchResults';

const baseContext: string = 'functional_BO_header_searchBar';

describe('BO - Header : Search bar', async () => {
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

  it('should search for "orders"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForOrders', baseContext);

    await dashboardPage.search(page, 'orders');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchResultsPage.pageTitle);
  });

  it('should check results for "orders"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForOrders', baseContext);

    const numberResults = await searchResultsPage.getNumberResults(page);
    expect(numberResults).to.be.eq(3);

    const numberFeatures = await searchResultsPage.getNumberResults(page, 'features');
    expect(numberFeatures).to.be.eq(1);

    const numberModules = await searchResultsPage.getNumberResults(page, 'modules');
    expect(numberModules).to.be.eq(2);

    const numberLinks = await searchResultsPage.getSearchPanelsLinksNumber(page);
    expect(numberLinks).to.be.eq(1);

    const linkHref = await searchResultsPage.getSearchPanelsLinkURL(page, 1);
    expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await searchResultsPage.getSearchPanelsLinkText(page, 1);
    expect(linkText).to.contains('Go to the documentation');
  });

  it('should search for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForJohnDoe', baseContext);

    await searchResultsPage.search(page, 'John Doe');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchResultsPage.pageTitle);
  });

  it('should check results for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForJohnDoe', baseContext);

    const numberResults = await searchResultsPage.getNumberResults(page);
    expect(numberResults).to.be.eq(1);

    const numberCustomers = await searchResultsPage.getNumberResults(page, 'customers');
    expect(numberCustomers).to.be.eq(1);

    const customerFirstName = await searchResultsPage.getTextColumn(page, 'customers', 1, 'firstname');
    expect(customerFirstName).to.be.eq('John');

    const customerName = await searchResultsPage.getTextColumn(page, 'customers', 1, 'name');
    expect(customerName).to.be.eq('DOE');

    const numberLinks = await searchResultsPage.getSearchPanelsLinksNumber(page);
    expect(numberLinks).to.be.eq(1);

    const linkHref = await searchResultsPage.getSearchPanelsLinkURL(page, 1);
    expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await searchResultsPage.getSearchPanelsLinkText(page, 1);
    expect(linkText).to.contains('Go to the documentation');
  });
});
