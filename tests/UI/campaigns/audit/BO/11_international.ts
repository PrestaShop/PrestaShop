import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boCountriesPage,
  boCountriesCreatePage,
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boDashboardPage,
  boGeolocationPage,
  boLoginPage,
  boLocalizationPage,
  boLanguagesPage,
  boLanguagesCreatePage,
  boStatesPage,
  boStatesCreatePage,
  boTaxesPage,
  boTaxesCreatePage,
  boTaxRulesPage,
  boTaxRulesCreatePage,
  boTranslationsPage,
  boZonesPage,
  boZonesCreatePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_international';

describe('BO - International', async () => {
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

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );
    await boLocalizationPage.closeSfToolBar(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Languages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await boLocalizationPage.goToSubTabLanguages(page);

    const pageTitle = await boLanguagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLanguagesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Languages > New Language\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewLanguages', baseContext);

    await boLanguagesPage.goToAddNewLanguage(page);

    const pageTitle = await boLanguagesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boLanguagesCreatePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Languages > Edit Language\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditLanguagesPage', baseContext);

    await boLocalizationPage.goToSubTabLanguages(page);
    await boLanguagesPage.goToEditLanguage(page, 1);

    const pageTitle = await boLanguagesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boLanguagesCreatePage.pageEditTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Currencies\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Currencies > New Currency\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

    await boCurrenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Currencies > Edit Currency\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrenciesPage', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);
    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.editCurrencyPage);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Localization > Geolocation\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeolocationPage', baseContext);

    await boLocalizationPage.goToSubTabGeolocation(page);

    const pageTitle = await boGeolocationPage.getPageTitle(page);
    expect(pageTitle).to.equal(boGeolocationPage.pageTitle);
  });

  it('should go to \'Locations > Zones\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToZonesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );
    await boLocalizationPage.closeSfToolBar(page);

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > Zones > New Zone\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewZonePage', baseContext);

    await boZonesPage.goToAddNewZonePage(page);

    const pageTitle = await boZonesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > Zones > Edit Zone\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goEditToZonesPage', baseContext);

    await boZonesCreatePage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );

    await boZonesPage.goToEditZonePage(page, 1);

    const pageTitle = await boZonesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await boZonesPage.goToSubTabCountries(page);

    const pageTitle = await boCountriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > Countries > New Country\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCountryPage', baseContext);

    await boCountriesPage.goToAddNewCountryPage(page);

    const pageTitle = await boCountriesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > Countries > Edit Country\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountriesPage', baseContext);

    await boZonesPage.goToSubTabCountries(page);
    await boCountriesPage.goToEditCountryPage(page, 1);

    const pageTitle = await boCountriesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await boZonesPage.goToSubTabStates(page);

    const pageTitle = await boStatesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > States > New State\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStatePage', baseContext);

    await boStatesPage.goToAddNewStatePage(page);

    const pageTitle = await boStatesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Locations > States > Edit State\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditStatesPage', baseContext);

    await boZonesPage.goToSubTabStates(page);
    await boStatesPage.goToEditStatePage(page, 1);

    const pageTitle = await boStatesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.taxesLink,
    );

    const pageTitle = await boTaxesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > Taxes > Add Tax\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewTax', baseContext);

    await boTaxesPage.goToAddNewTaxPage(page);

    const pageTitle = await boTaxesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > Taxes > Edit Tax\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditTaxesPage', baseContext);

    await boTaxesCreatePage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.taxesLink,
    );
    await boTaxesPage.goToEditTaxPage(page, 1);

    const pageTitle = await boTaxesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > TaxRules\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

    await boTaxesPage.goToTaxRulesPage(page);

    const pageTitle = await boTaxRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxRulesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > TaxRules > Add Tax Rule Group\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePageToCreate', baseContext);

    await boTaxRulesPage.goToAddNewTaxRulesGroupPage(page);

    const pageTitle = await boTaxRulesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxRulesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'International > TaxRules > Edit Tax Rule Group\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditTaxRulesPage', baseContext);

    await boTaxesPage.goToTaxRulesPage(page);
    await boTaxRulesPage.goToEditTaxRulePage(page, 1);

    const pageTitle = await boTaxRulesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxRulesCreatePage.pageTitleEdit);
  });

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.translationsLink,
    );

    const pageTitle = await boTranslationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
  });
});
