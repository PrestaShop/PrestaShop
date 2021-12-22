require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');

const baseContext = 'functional_BO_shopParameters_general_general_enableDisableMultiStore';

let browserContext;
let page;

/*
Enable/Disable multistore
Check the existence of multistore page
 */
describe('BO - Shop Parameters - General : Enable/Disable multi store', async () => {
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

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.shopParametersGeneralLink,
    );

    await generalPage.closeSfToolBar(page);

    const pageTitle = await generalPage.getPageTitle(page);
    await expect(pageTitle).to.contains(generalPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} multi store`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}MultiStore`, baseContext);

      const result = await generalPage.setMultiStoreStatus(page, test.args.exist);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });

    it('should check the existence of \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToMultiStorePage_${index}`, baseContext);

      const result = await generalPage.isSubmenuVisible(
        page,
        generalPage.advancedParametersLink,
        generalPage.multistoreLink,
      );

      await expect(result).to.be.equal(test.args.exist);
    });
  });
});
