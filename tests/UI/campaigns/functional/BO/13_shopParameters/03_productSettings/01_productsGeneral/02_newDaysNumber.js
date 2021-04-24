require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const homePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_productsGeneral_newDaysNumber';

let browserContext;
let page;

/*
Update new days number to 0
Check that there is no new products in FO
Go back to the default value
Check that all products are new in FO
 */
describe('Number of days for which the product is considered \'new\'', async () => {
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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {value: 0, exist: false, state: 'NotVisible'}},
    {args: {value: 20, exist: true, state: 'Visible'}},
  ];

  tests.forEach((test) => {
    it(`should update Number of days to ${test.args.value}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateNumberOfDaysTo${test.args.value}`, baseContext);

      const result = await productSettingsPage.updateNumberOfDays(page, test.args.value);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should check the new flag in the product miniature in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNewFlagIs${test.args.state}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      const isNewFlagVisible = await homePage.isNewFlagVisible(page, 1);
      await expect(isNewFlagVisible).to.be.equal(test.args.exist);

      page = await homePage.closePage(browserContext, page, 0);
    });
  });
});
