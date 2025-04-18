import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boCountriesCreatePage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataCustomers,
  FakerCountry,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyAddressesPage,
  foClassicMyAddressesCreatePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const countryDataIncorrectDate: FakerCountry = new FakerCountry({
    name: 'countryTest',
    isoCode: 'MC',
    callPrefix: '+99',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const createCountryData: FakerCountry = new FakerCountry({
    name: 'countryTest',
    isoCode: 'CT',
    callPrefix: '216',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: true,
  });
  const editCountryData: FakerCountry = new FakerCountry({
    name: 'countryTestEdit',
    isoCode: 'CT',
    callPrefix: '333',
    currency: 'Euro',
    zipCodeFormat: 'NNNN',
    active: false,
  });

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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );
    await boZonesPage.closeSfToolBar(page);

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);
  });

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await boZonesPage.goToSubTabCountries(page);

    const pageTitle = await boCountriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesPage.pageTitle);
  });

  it('should reset all filters and get number of countries in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCountries = await boCountriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountries).to.be.above(0);
  });

  describe('Create country', async () => {
    it('should go to add new country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCountryPage', baseContext);

      await boCountriesPage.goToAddNewCountryPage(page);

      const pageTitle = await boCountriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesCreatePage.pageTitleCreate);
    });

    it('should try to create new country with a used ISO code and an invalid prefix', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreateNewCountry', baseContext);

      const textResult = await boCountriesCreatePage.createEditCountry(page, countryDataIncorrectDate);
      expect(textResult).to.contain(boCountriesCreatePage.errorMessageIsoCode)
        .and.contains(boCountriesCreatePage.errorMessagePrefix);
    });

    it('should create new country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewCountry', baseContext);

      const textResult = await boCountriesCreatePage.createEditCountry(page, createCountryData);
      expect(textResult).to.contain(boCountriesPage.successfulCreationMessage);

      const numberOfCountriesAfterCreation = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterCreation).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await boCountriesPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_1', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_1', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_1', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open addresses page').to.contains(foClassicMyAddressesPage.pageTitle);
    });

    it(`should check that the new country '${createCountryData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewCountryExist', baseContext);

      await foClassicMyAddressesPage.openNewAddressForm(page);

      const countryExist = await foClassicMyAddressesCreatePage.countryExist(page, createCountryData.name);
      expect(countryExist, 'Country does not exist').to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO_1', baseContext);

      await foClassicMyAddressesPage.logout(page);

      const isCustomerConnected = await foClassicMyAddressesPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_1', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCountriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesPage.pageTitle);
    });
  });

  describe('Update country', async () => {
    it(`should filter country by name '${createCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boCountriesPage.filterTable(page, 'input', 'b!name', createCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(createCountryData.name);
    });

    it('should go to edit country page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPage', baseContext);

      await boCountriesPage.goToEditCountryPage(page, 1);

      const pageTitle = await boCountriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesCreatePage.pageTitleEdit);
    });

    it('should edit country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCountry', baseContext);

      const textResult = await boCountriesCreatePage.createEditCountry(page, editCountryData);
      expect(textResult).to.to.contains(boCountriesPage.successfulUpdateMessage);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries + 1);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View my shop and init pages
      page = await boCountriesPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO_2', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO_2', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage_2', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open addresses page').to.contains(foClassicMyAddressesPage.pageTitle);
    });

    it(`should check that the edited country '${editCountryData.name}' not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsCountryNotExist', baseContext);

      await foClassicMyAddressesPage.openNewAddressForm(page);

      const countryExist = await foClassicMyAddressesCreatePage.countryExist(page, editCountryData.name);
      expect(countryExist, 'Country exist').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCountriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesPage.pageTitle);
    });
  });

  describe('Delete country', async () => {
    it(`should filter country by name '${editCountryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boCountriesPage.filterTable(page, 'input', 'b!name', editCountryData.name);

      // Check number of countries
      const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCountriesAfterFilter).to.be.at.least(1);

      const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
      expect(textColumn).to.contains(editCountryData.name);
    });

    it('should delete country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCountry', baseContext);

      const textResult = await boCountriesPage.deleteCountriesByBulkActions(page);
      expect(textResult).to.to.contains(boCountriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCountriesAfterReset).to.be.equal(numberOfCountries);
    });
  });
});
