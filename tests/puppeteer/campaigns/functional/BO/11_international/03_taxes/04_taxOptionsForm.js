require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const taxOptions = require('@data/demo/taxOptions');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/international/taxes');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    taxesPage: new TaxesPage(page),
  };
};

// Edit Tax options
describe('Edit Tax options with all EcoTax values', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to taxes page
  loginCommon.loginBO();
  it('should go to Taxes page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.taxesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });
  // Testing all options of EcoTax
  describe('Edit tax options', async () => {
    taxOptions.forEach((taxOption) => {
      it(`should edit Tax Option,
      \tEnable Tax:${taxOption.enabled},
      \tDisplay tax in the shopping cart: '${taxOption.displayInShoppingCart}',
      \tBased on: '${taxOption.basedOn}',
      \tUse ecotax: '${taxOption.useEcoTax}',
      \tEcotax: '${taxOption.ecoTax}'`, async function () {
        const textResult = await this.pageObjects.taxesPage.updateTaxOption(taxOption);
        await expect(textResult).to.be.equal('Update successful');
      });
    });
  });
});
