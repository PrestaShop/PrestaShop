import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataCountries,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyAddressesPage,
  foClassicMyAddressesCreatePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it(`should search for the country '${dataCountries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToEnable', baseContext);

    await boCountriesPage.filterTable(page, 'input', 'b!name', dataCountries.afghanistan.name);

    const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCountriesAfterFilter).to.be.equal(1);

    const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
    expect(textColumn).to.equal(dataCountries.afghanistan.name);
  });

  it(`should enable the country '${dataCountries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCountry', baseContext);

    await boCountriesPage.setCountryStatus(page, 1, true);

    const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(true);
  });

  [
    {args: {status: 'enable', enable: true, isCountryVisible: false}},
    {args: {status: 'disable', enable: false, isCountryVisible: true}},
  ].forEach((status, index: number) => {
    it(`should ${status.args.status} restrict country selections`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}RestrictCountry`, baseContext);

      const currentStatus = await boCountriesPage.setCountriesRestrictions(page, status.args.enable);
      expect(currentStatus).to.contains(boCountriesPage.settingsUpdateMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

      // Click on view my shop
      page = await boCountriesPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `login${index}`, baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddressesPage${index}`, baseContext);

      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open addresses page').to.contains(foClassicMyAddressesPage.pageTitle);
    });

    it(`should check if the country '${dataCountries.afghanistan.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIsNewCountryExist${index}`, baseContext);

      await foClassicMyAddressesPage.openNewAddressForm(page);

      const countryExist = await foClassicMyAddressesCreatePage.countryExist(page, dataCountries.afghanistan.name);
      expect(countryExist).to.equal(status.args.isCountryVisible);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

      await foClassicMyAddressesPage.logout(page);

      const isCustomerConnected = await foClassicMyAddressesPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

      page = await foClassicMyAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await boCountriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCountriesPage.pageTitle);
    });
  });

  it(`should search for the country '${dataCountries.afghanistan.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDisable', baseContext);

    await boCountriesPage.filterTable(page, 'input', 'b!name', dataCountries.afghanistan.name);

    const numberOfCountriesAfterFilter = await boCountriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCountriesAfterFilter).to.be.equal(1);

    const textColumn = await boCountriesPage.getTextColumnFromTable(page, 1, 'b!name');
    expect(textColumn).to.equal(dataCountries.afghanistan.name);
  });

  it('should disable the country', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCountry', baseContext);

    await boCountriesPage.setCountryStatus(page, 1, false);

    const currentStatus = await boCountriesPage.getCountryStatus(page, 1);
    expect(currentStatus).to.eq(false);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDisable', baseContext);

    const numberOfCountriesAfterReset = await boCountriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCountriesAfterReset).to.equal(numberOfCountries);
  });
});
