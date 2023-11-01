// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import addressesPage from '@pages/BO/customers/addresses';
import addAddressPage from '@pages/BO/customers/addresses/add';

// Import Data
import Countries from '@data/demo/countries';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const countriesToTest: string[] = [Countries.netherlands.name, Countries.france.name];

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

  countriesToTest.forEach((country: string, index: number) => {
    describe(`Set default country to '${country}' and check result`, async () => {
      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLocalizationPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );

        const pageTitle = await localizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it(`should set default country to '${country}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCountry${index}`, baseContext);

        const textResult = await localizationPage.setDefaultCountry(page, country);
        expect(textResult).to.contain(localizationPage.successfulSettingsUpdateMessage);
      });

      it('should go to addresses page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddressesPage${index}`, baseContext);

        await localizationPage.goToSubMenu(
          page,
          localizationPage.customersParentLink,
          localizationPage.addressesLink,
        );

        const pageTitle = await addressesPage.getPageTitle(page);
        expect(pageTitle).to.contains(addressesPage.pageTitle);
      });

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await addressesPage.goToAddNewAddressPage(page);

        const pageTitle = await addAddressPage.getPageTitle(page);
        expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
      });

      it('should check default country', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSelectedCountry${index}`, baseContext);

        const selectedCountry = await addAddressPage.getSelectedCountry(page);
        expect(selectedCountry).to.equal(country);
      });
    });
  });
});
