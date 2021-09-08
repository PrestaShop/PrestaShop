require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const addressesPage = require('@pages/BO/customers/addresses');
const addAddressPage = require('@pages/BO/customers/addresses/add');

// Import Data
const {countries} = require('@data/demo/countries');

const countriesToTest = [countries.netherlands.name, countries.france.name];

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_defaultCountry';

let browserContext;
let page;

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

  countriesToTest.forEach((country, index) => {
    describe(`Set default country to '${country}' and check result`, async () => {
      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLocalizationPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );

        const pageTitle = await localizationPage.getPageTitle(page);
        await expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it(`should set default country to '${country}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCountry${index}`, baseContext);

        const textResult = await localizationPage.setDefaultCountry(page, country);
        await expect(textResult).to.contain(localizationPage.successfulSettingsUpdateMessage);
      });

      it('should go to addresses page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddressesPage${index}`, baseContext);

        await localizationPage.goToSubMenu(
          page,
          localizationPage.customersParentLink,
          localizationPage.addressesLink,
        );

        const pageTitle = await addressesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addressesPage.pageTitle);
      });

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await addressesPage.goToAddNewAddressPage(page);
        const pageTitle = await addAddressPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
      });

      it('should check default country', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSelectedCountry${index}`, baseContext);

        const selectedCountry = await addAddressPage.getSelectedCountry(page);
        await expect(selectedCountry).to.equal(country);
      });
    });
  });
});
