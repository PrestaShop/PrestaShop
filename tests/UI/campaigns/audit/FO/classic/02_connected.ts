import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyAddressesPage,
  foClassicMyAddressesCreatePage,
  foClassicMyCreditSlipsPage,
  foClassicMyGDPRPersonalDataPage,
  foClassicMyInformationsPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicMyWishlistsPage,
  foClassicMyWishlistsViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_FO_classic_connected';

describe('Check FO connected pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should go to the home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHome', baseContext);

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'customerLogin', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "Your personal information" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToInformationPage', baseContext);

    await foClassicMyAccountPage.goToInformationPage(page);

    const pageTitle = await foClassicMyInformationsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyInformationsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "Your addresses" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await foClassicMyInformationsPage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToAddressesPage(page);

    const pageHeaderTitle = await foClassicMyAddressesPage.getPageTitle(page);
    expect(pageHeaderTitle).to.equal(foClassicMyAddressesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "New address" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

    await foClassicMyAddressesPage.openNewAddressForm(page);

    const pageHeaderTitle = await foClassicMyAddressesCreatePage.getHeaderTitle(page);
    expect(pageHeaderTitle).to.equal(foClassicMyAddressesCreatePage.creationFormTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "Order history" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHistoryAndDetailsPage', baseContext);

    await foClassicMyInformationsPage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

    const pageTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicMyOrderHistoryPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "Order details" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDetailsPage', baseContext);

    await foClassicMyOrderHistoryPage.goToDetailsPage(page, 1);

    const pageTitle = await foClassicMyOrderDetailsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyOrderDetailsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "Credit slips" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

    await foClassicMyInformationsPage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToCreditSlipsPage(page);

    const pageTitle = await foClassicMyCreditSlipsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyCreditSlipsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "My wishlists" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlistsPage', baseContext);

    await foClassicMyInformationsPage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToMyWishlistsPage(page);

    const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "My wishlist" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWishlistPage', baseContext);

    await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

    const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
    expect(pageTitle).to.contains('My wishlist');

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the "GDPR - Personal data" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMyGDPRPersonalDataPage', baseContext);

    await foClassicMyInformationsPage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToMyGDPRPersonalDataPage(page);

    const pageTitle = await foClassicMyGDPRPersonalDataPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyGDPRPersonalDataPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should logout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logout', baseContext);

    await foClassicMyGDPRPersonalDataPage.logout(page);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
