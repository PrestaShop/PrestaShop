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
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {addressesPage} from '@pages/FO/classic/myAccount/addresses';
import {addAddressPage} from '@pages/FO/classic/myAccount/addAddress';

// Import data
import CountryData from '@data/faker/country';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_countries_CRUDCountry';

/*
Scenario:
- Try to create country with used iso code and invalid prefix
- Create enabled country
- Go to FO > Login > go to addresses > new address form and check the new country
- Update country (disable country)
- Go to FO > Login > go to addresses > new address form and check that the new country is not visible
- Delete country by bulk actions
 */
describe('BO - International - Countries : CRUD country', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCountries: number = 0;

  const countryDataIncorrectDate: CountryData = new CountryData({
    name: 'countryTest',
    isoCode: 'MC',
    callPrefix: '+99',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const createCountryData: CountryData = new CountryData({
    name: 'countryTest',
    isoCode: 'CT',
    callPrefix: '216',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const editCountryData: CountryData = new CountryData({
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
    expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await zonesPage.goToSubTabCountries(page);

    const pageTitle = await countriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(countriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  describe('Create country', async () => {
    it('should go to add new country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCountryPage', baseContext);

      await countriesPage.goToAddNewCountryPage(page);

      const pageTitle = await addCountryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCountryPage.pageTitleCreate);
    });

    it('should try to create new country with a used ISO code and an invalid prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreateNewCountry', baseContext);

      const textResult = await addCountryPage.createEditCountry(page, countryDataIncorrectDate);
      expect(textResult).to.to.contains(addCountryPage.errorMessageIsoCode).and.contains(addCountryPage.errorMessagePrefix);
    });

    it('should create new country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewCountry', baseContext);

      const textResult = await addCountryPage.createEditCountry(page, createCountryData);
      expect(textResult).to.to.contains(countriesPage.successfulCreationMessage);

      const numberOfCountriesAfterCreation = await countriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterCreation).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await countriesPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_1', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_1', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_1', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await myAccountPage.goToAddressesPage(page);

      const pageTitle = await addressesPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open addresses page').to.contains(addressesPage.pageTitle);
    });

    it(`should check that the new country '${createCountryData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewCountryExist', baseContext);

      await addressesPage.openNewAddressForm(page);

      const countryExist = await addAddressPage.countryExist(page, createCountryData.name);
      expect(countryExist, 'Country does not exist').to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO_1', baseContext);

      await addressesPage.logout(page);

      const isCustomerConnected = await addressesPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_1', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await countriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(countriesPage.pageTitle);
    });
  });

  describe('Update country', async () => {
    it(`should filter country by name '${createCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await countriesPage.filterTable(page, 'input', 'b!name', createCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(createCountryData.name);
    });

    it('should go to edit country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPage', baseContext);

      await countriesPage.goToEditCountryPage(page, 1);

      const pageTitle = await addCountryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCountryPage.pageTitleEdit);
    });

    it('should edit country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCountry', baseContext);

      const textResult = await addCountryPage.createEditCountry(page, editCountryData);
      expect(textResult).to.to.contains(countriesPage.successfulUpdateMessage);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View my shop and init pages
      page = await countriesPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_2', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_2', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_2', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await myAccountPage.goToAddressesPage(page);

      const pageTitle = await addressesPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open addresses page').to.contains(addressesPage.pageTitle);
    });

    it(`should check that the edited country '${editCountryData.name}' not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsCountryNotExist', baseContext);

      await addressesPage.openNewAddressForm(page);

      const countryExist = await addAddressPage.countryExist(page, editCountryData.name);
      expect(countryExist, 'Country exist').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await countriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(countriesPage.pageTitle);
    });
  });

  describe('Delete country', async () => {
    it(`should filter country by name '${editCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await countriesPage.filterTable(page, 'input', 'b!name', editCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(editCountryData.name);
    });

    it('should delete country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCountry', baseContext);

      const textResult = await countriesPage.deleteCountriesByBulkActions(page);
      expect(textResult).to.to.contains(countriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCountriesAfterReset = await countriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries);
    });
  });
});
