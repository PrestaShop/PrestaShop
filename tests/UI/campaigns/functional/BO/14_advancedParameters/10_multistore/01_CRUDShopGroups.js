require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@page/BO/shopParameters');
const multiStorePage = require('@pages/BO/advancedParameters/multistore');
const addShopGroupPage = require('@pages/BO/advancedParameters/multistore/add');

// Import data
const ShopGroupFaker = require('@data/faker/shopGroup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_webservice_CRUDShopGroups';

let browserContext;
let page;

let numberOfWebserviceKeys = 0;

const createShopGroupData = new ShopGroupFaker({});

// Create, Read, Update and Delete webservice key in BO
describe('Create, Read, Update and Delete shop groups in BO', async () => {
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

  describe('Enable multi store', async () => {
    it('should go to "Shop parameters > General" page', async function () {
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

    it('should enable "Multi store"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, true);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });
});
