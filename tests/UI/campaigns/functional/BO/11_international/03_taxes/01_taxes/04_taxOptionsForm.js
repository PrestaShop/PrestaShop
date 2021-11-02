require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const taxOptions = require('@data/demo/taxOptions');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_taxes_taxes_taxOptionsForm';

let browserContext;
let page;

// Edit Tax options
describe('BO - International - Taxes : Edit Tax options with all EcoTax values', async () => {
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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );

    await taxesPage.closeSfToolBar(page);

    const pageTitle = await taxesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(taxesPage.pageTitle);
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

        const textResult = await taxesPage.updateTaxOption(page, taxOption);
        await expect(textResult).to.be.equal('Update successful');
      });
    });
  });
});
