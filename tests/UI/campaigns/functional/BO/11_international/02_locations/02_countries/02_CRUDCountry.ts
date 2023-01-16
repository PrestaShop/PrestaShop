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
import addCountryPage from '@pages/BO/international/locations/countries/add';
// Import FO pages
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foMyAccountPage from '@pages/FO/myAccount';
import foAddressesPage from '@pages/FO/myAccount/addresses';
import foNewAddressesPage from '@pages/FO/myAccount/addAddress';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import CountryFaker from '@data/faker/country';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_countries_CRUDCountry';

/*
Create country
Update country
Delete country
 */
describe('BO - International - Countries : CRUD country', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCountries: number = 0;

  const createCountryData: CountryFaker = new CountryFaker({
    name: 'countryTest',
    isoCode: 'CT',
    callPrefix: '216',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const editCountryData:CountryFaker = new CountryFaker({
    name: 'countryTestEdit',
    isoCode: 'CT',
    callPrefix: '333',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: false,
  });

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

  describe('Create country', async () => {
    it('should go to add new country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCountryPage', baseContext);

      await countriesPage.goToAddNewCountryPage(page);

      const pageTitle = await addCountryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCountryPage.pageTitleCreate);
    });

    it('should create new country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewCountry', baseContext);

      const textResult = await addCountryPage.createEditCountry(page, createCountryData);
      await expect(textResult).to.to.contains(countriesPage.successfulCreationMessage);

      const numberOfCountriesAfterCreation = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterCreation).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await countriesPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_1', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_1', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_1', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToAddressesPage(page);

      const pageTitle = await foAddressesPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open addresses page').to.contains(foAddressesPage.pageTitle);
    });

    it(`should check that the new country '${createCountryData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewCountryExist', baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const countryExist = await foNewAddressesPage.countryExist(page, createCountryData.name);
      await expect(countryExist, 'Country does not exist').to.be.true;
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO_1', baseContext);

      await foAddressesPage.logout(page);

      const isCustomerConnected = await foAddressesPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_1', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await countriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(countriesPage.pageTitle);
    });
  });

  describe('Update country', async () => {
    it(`should filter country by name '${createCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await countriesPage.filterTable(page, 'input', 'b!name', createCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      await expect(textColumn).to.contains(createCountryData.name);
    });

    it('should go to edit country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPage', baseContext);

      await countriesPage.goToEditCountryPage(page, 1);
      const pageTitle = await addCountryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCountryPage.pageTitleEdit);
    });

    it('should edit country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCountry', baseContext);

      const textResult = await addCountryPage.createEditCountry(page, editCountryData);
      await expect(textResult).to.to.contains(countriesPage.successfulUpdateMessage);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View my shop and init pages
      page = await countriesPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_2', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_2', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_2', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToAddressesPage(page);

      const pageTitle = await foAddressesPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open addresses page').to.contains(foAddressesPage.pageTitle);
    });

    it(`should check that the edited country '${editCountryData.name}' not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsCountryNotExist', baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const countryExist = await foNewAddressesPage.countryExist(page, editCountryData.name);
      await expect(countryExist, 'Country exist').to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await countriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(countriesPage.pageTitle);
    });
  });

  describe('Delete country by bulk actions', async () => {
    it(`should filter country by name '${editCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await countriesPage.filterTable(page, 'input', 'b!name', editCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      await expect(textColumn).to.contains(editCountryData.name);
    });

    it('should delete country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCountry', baseContext);

      const textResult = await countriesPage.deleteCountriesByBulkActions(page);
      await expect(textResult).to.to.contains(countriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries);
    });
  });
});
