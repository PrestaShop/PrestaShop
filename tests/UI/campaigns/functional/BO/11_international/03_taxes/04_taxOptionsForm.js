require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const taxOptions = require('@data/demo/taxOptions');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_taxes_taxOptionsForm';

let browser;
let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    taxesPage: new TaxesPage(page),
  };
};

// Edit Tax options
describe('Edit Tax options with all EcoTax values', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to taxes page
  loginCommon.loginBO();

  it('should go to Taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.taxesLink,
    );

    await this.pageObjects.taxesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });

  // Testing all options of EcoTax
  describe('Edit tax options', async () => {
    taxOptions.forEach((taxOption, index) => {
      it(`should edit Tax Option,
      \tEnable Tax:${taxOption.enabled},
      \tDisplay tax in the shopping cart: '${taxOption.displayInShoppingCart}',
      \tBased on: '${taxOption.basedOn}',
      \tUse ecotax: '${taxOption.useEcoTax}',
      \tEcotax: '${taxOption.ecoTax}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateForm${index + 1}`, baseContext);

        const textResult = await this.pageObjects.taxesPage.updateTaxOption(taxOption);
        await expect(textResult).to.be.equal('Update successful');
      });
    });
  });
});
