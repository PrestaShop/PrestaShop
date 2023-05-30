// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import countriesPage from '@pages/BO/international/locations/countries';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';
import addressesPage from '@pages/FO/myAccount/addresses';
import newAddressesPage from '@pages/FO/myAccount/addAddress';

// Import data
import Countries from '@data/demo/countries';
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_countries_countriesRestrictions';

/*
Enable the country 'Afghanistan'
Enable 'Restrict country selections in front office to those covered by active carriers'
Go to FO > Address page and check that the country doesn't exist
Disable 'Restrict country selections in front office to those covered by active carriers'
Go to FO > Address page and check that the country exist
Disable the country 'Afghanistan'
 */
describe('BO - International - Countries : Restrict country selections in front office', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCountries: number = 0;

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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.locationsLink,
    );
    await zonesPage.closeSfToolBar(page);

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);

    const pageTitle = await countriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCountries).to.be.above(0);
  });

  it(`should search for the country '${Countries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

    await countriesPage.filterTable(page, 'input', 'b!name', Countries.afghanistan.name);

    const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCountriesAfterFilter).to.be.equal(1);

    const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
    await expect(textColumn).to.equal(Countries.afghanistan.name);
  });

  it(`should enable the country '${Countries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCountry', baseContext);

    await countriesPage.setCountryStatus(page, 1, true);

    const currentStatus = await countriesPage.getCountryStatus(page, 1);
    await expect(currentStatus).to.be.true;
  });

  [
    {args: {status: 'enable', enable: true, isCountryVisible: false}},
    {args: {status: 'disable', enable: false, isCountryVisible: true}},
  ].forEach((status, index: number) => {
    it(`should ${status.args.status} restrict country selections`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}RestrictCountry`, baseContext);

      const currentStatus = await countriesPage.setCountriesRestrictions(page, status.args.enable);
      await expect(currentStatus).to.contains(countriesPage.settingsUpdateMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

      // Click on view my shop
      page = await countriesPage.viewMyShop(page);
      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `login${index}`, baseContext);

      await homePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddressesPage${index}`, baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageTitle = await addressesPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open addresses page').to.contains(addressesPage.pageTitle);
    });

    it(`should check if the country '${Countries.afghanistan.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIsNewCountryExist${index}`, baseContext);

      await addressesPage.openNewAddressForm(page);

      const countryExist = await newAddressesPage.countryExist(page, Countries.afghanistan.name);
      await expect(countryExist).to.equal(status.args.isCountryVisible);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

      await addressesPage.logout(page);

      const isCustomerConnected = await addressesPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

      page = await myAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await countriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(countriesPage.pageTitle);
    });
  });

  it(`should search for the country '${Countries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDisable', baseContext);

    await countriesPage.filterTable(page, 'input', 'b!name', Countries.afghanistan.name);

    const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCountriesAfterFilter).to.be.equal(1);

    const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
    await expect(textColumn).to.equal(Countries.afghanistan.name);
  });

  it('should disable the country', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCountry', baseContext);

    await countriesPage.setCountryStatus(page, 1, false);

    const currentStatus = await countriesPage.getCountryStatus(page, 1);
    await expect(currentStatus).to.be.false;
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDisable', baseContext);

    const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
  });
});
