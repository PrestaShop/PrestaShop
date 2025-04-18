import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAddressesPage,
  boAddressesCreatePage,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  dataCountries,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_localization_defaultCountry';

/*
Go to Localization page
Change default country to 'Netherlands'
Go to add address page
Check that selected country is 'Netherlands'
Go to Localization page
Reset default country to 'France'
Go to add address page
Check that selected country is 'France'
 */
describe('BO - International - Localization : Update default country', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const countriesToTest: string[] = [dataCountries.netherlands.name, dataCountries.france.name];

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

  countriesToTest.forEach((country: string, index: number) => {
    describe(`Set default country to '${country}' and check result`, async () => {
      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLocalizationPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.internationalParentLink,
          boDashboardPage.localizationLink,
        );

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      it(`should set default country to '${country}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCountry${index}`, baseContext);

        const textResult = await boLocalizationPage.setDefaultCountry(page, country);
        expect(textResult).to.contain(boLocalizationPage.successfulSettingsUpdateMessage);
      });

      it('should go to addresses page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddressesPage${index}`, baseContext);

        await boLocalizationPage.goToSubMenu(
          page,
          boLocalizationPage.customersParentLink,
          boLocalizationPage.addressesLink,
        );

        const pageTitle = await boAddressesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddressesPage.pageTitle);
      });

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await boAddressesPage.goToAddNewAddressPage(page);

        const pageTitle = await boAddressesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
      });

      it('should check default country', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSelectedCountry${index}`, baseContext);

        const selectedCountry = await boAddressesCreatePage.getSelectedCountry(page);
        expect(selectedCountry).to.equal(country);
      });
    });
  });
});
