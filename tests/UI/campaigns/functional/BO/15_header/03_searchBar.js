require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchResultsPage = require('@pages/BO/searchResults');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_header_searchBar';

let browserContext;
let page;

describe('BO - Header : Search bar', async () => {
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
    await expect(pageTitle).to.contains(searchResultsPage.pageTitle);
  });

  it('should check results for "orders"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForOrders', baseContext);

    const numberResults = await searchResultsPage.getNumberResults(page);
    await expect(numberResults).to.be.eq(3);

    const numberFeatures = await searchResultsPage.getNumberResults(page, 'features');
    await expect(numberFeatures).to.be.eq(1);

    const numberModules = await searchResultsPage.getNumberResults(page, 'modules');
    await expect(numberModules).to.be.eq(2);

    const numberLinks = await searchResultsPage.getSearchPanelsLinksNumber(page);
    await expect(numberLinks).to.be.eq(1);

    const linkHref = await searchResultsPage.getSearchPanelsLinkURL(page, 1);
    await expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await searchResultsPage.getSearchPanelsLinkText(page, 1);
    await expect(linkText).to.contains('Go to the documentation');
  });

  it('should search for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForJohnDoe', baseContext);
    await searchResultsPage.search(page, 'John Doe');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchResultsPage.pageTitle);
  });

  it('should check results for "John Doe"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkResultsForJohnDoe', baseContext);

    const numberResults = await searchResultsPage.getNumberResults(page);
    await expect(numberResults).to.be.eq(1);

    const numberCustomers = await searchResultsPage.getNumberResults(page, 'customers');
    await expect(numberCustomers).to.be.eq(1);

    const customerFirstName = await searchResultsPage.getTextColumn(page, 'customers', 1, 'firstname');
    await expect(customerFirstName).to.be.eq('John');

    const customerName = await searchResultsPage.getTextColumn(page, 'customers', 1, 'name');
    await expect(customerName).to.be.eq('DOE');

    const numberLinks = await searchResultsPage.getSearchPanelsLinksNumber(page);
    await expect(numberLinks).to.be.eq(1);

    const linkHref = await searchResultsPage.getSearchPanelsLinkURL(page, 1);
    await expect(linkHref).to.contains('https://docs.prestashop-project.org/welcome/?q=');

    const linkText = await searchResultsPage.getSearchPanelsLinkText(page, 1);
    await expect(linkText).to.contains('Go to the documentation');
  });
});
